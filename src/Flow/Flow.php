<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Flow;

use Illuminate\Container\Container;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\MarkingStore\MultipleStateMarkingStore;
use Symfony\Component\Workflow\Transition;

/**
 * Class Flow.
 */
class Flow
{
    /**
     * @var \Symfony\Component\Workflow\Definition
     */
    protected $definition;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\Workflow\MarkingStore\MultipleStateMarkingStore
     */
    protected $markingStore;

    /**
     * @var string
     */
    protected $name;

    /**
     * Flow constructor.
     *
     * @param \Symfony\Component\Workflow\Definition                              $definition
     * @param \Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface|null $markingStore
     * @param string                                                              $name
     */
    public function __construct(Definition $definition, MarkingStoreInterface $markingStore = null, $name = 'unnamed')
    {
        $this->definition = $definition;
        $this->markingStore = $markingStore ?: new MultipleStateMarkingStore();
        $this->dispatcher = Container::getInstance()->make('events');
        $this->name = $name;
    }

    /**
     * @param                                        $subject
     * @param \Symfony\Component\Workflow\Transition $initialTransition
     * @param \Symfony\Component\Workflow\Marking    $marking
     */
    private function announce($subject, Transition $initialTransition, Marking $marking)
    {
        if (null === $this->dispatcher) {
            return;
        }
        $event = new Event($subject, $marking, $initialTransition);
        foreach ($this->getEnabledTransitions($subject) as $transition) {
            $this->dispatcher->dispatch(sprintf('flow.%s.announce.%s', $this->name, $transition->getName()), $event);
        }
    }

    public function apply($subject, $transitionName)
    {
        $transitions = $this->getEnabledTransitions($subject);
        // We can shortcut the getMarking method in order to boost performance,
        // since the "getEnabledTransitions" method already checks the Marking
        // state
        $marking = $this->markingStore->getMarking($subject);
        $applied = false;
        foreach ($transitions as $transition) {
            if ($transitionName !== $transition->getName()) {
                continue;
            }
            $applied = true;
            $this->leave($subject, $transition, $marking);
            $this->transition($subject, $transition, $marking);
            $this->enter($subject, $transition, $marking);
            $this->markingStore->setMarking($subject, $marking);
            $this->entered($subject, $transition, $marking);
            $this->announce($subject, $transition, $marking);
        }
        if (!$applied) {
            throw new LogicException(sprintf('Unable to apply transition "%s" for workflow "%s".', $transitionName, $this->name));
        }

        return $marking;
    }

    /**
     * @param object $subject
     * @param string $transitionName
     *
     * @return bool
     */
    public function can($subject, $transitionName)
    {
        $transitions = $this->getEnabledTransitions($subject);
        foreach ($transitions as $transition) {
            if ($transitionName === $transition->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param                                        $subject
     * @param \Symfony\Component\Workflow\Marking    $marking
     * @param \Symfony\Component\Workflow\Transition $transition
     *
     * @return bool
     */
    private function doCan($subject, Marking $marking, Transition $transition)
    {
        foreach ($transition->getFroms() as $place) {
            if (!$marking->has($place)) {
                return false;
            }
        }
        if (true === $this->guardTransition($subject, $marking, $transition)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $dumper = new GraphvizDumper();

        return $dumper->dump($this->definition);
    }

    /**
     * @param                                        $subject
     * @param \Symfony\Component\Workflow\Transition $transition
     * @param \Symfony\Component\Workflow\Marking    $marking
     */
    private function enter($subject, Transition $transition, Marking $marking)
    {
        $places = $transition->getTos();
        if (null !== $this->dispatcher) {
            $event = new Event($subject, $marking, $transition, $this->name);
            $this->dispatcher->dispatch('flow.enter', $event);
            $this->dispatcher->dispatch(sprintf('flow.%s.enter', $this->name), $event);
            foreach ($places as $place) {
                $this->dispatcher->dispatch(sprintf('flow.%s.enter.%s', $this->name, $place), $event);
            }
        }
        foreach ($places as $place) {
            $marking->mark($place);
        }
    }

    /**
     * @param                                        $subject
     * @param \Symfony\Component\Workflow\Transition $transition
     * @param \Symfony\Component\Workflow\Marking    $marking
     */
    private function entered($subject, Transition $transition, Marking $marking)
    {
        if (null === $this->dispatcher) {
            return;
        }
        $event = new Event($subject, $marking, $transition, $this->name);
        $this->dispatcher->dispatch('flow.entered', $event);
        $this->dispatcher->dispatch(sprintf('flow.%s.entered', $this->name), $event);
        foreach ($transition->getTos() as $place) {
            $this->dispatcher->dispatch(sprintf('flow.%s.entered.%s', $this->name, $place), $event);
        }
    }

    /**
     * @param object $subject
     *
     * @return array
     */
    public function getEnabledTransitions($subject)
    {
        $enabled = [];
        $marking = $this->getMarking($subject);
        foreach ($this->definition->getTransitions() as $transition) {
            if ($this->doCan($subject, $marking, $transition)) {
                $enabled[] = $transition;
            }
        }

        return $enabled;
    }

    /**
     * @param object $subject
     *
     * @return \Symfony\Component\Workflow\Marking
     */
    public function getMarking($subject)
    {
        $marking = $this->markingStore->getMarking($subject);
        if (!$marking instanceof Marking) {
            throw new LogicException(sprintf('The value returned by the MarkingStore is not an instance of "%s" for workflow "%s".', Marking::class, $this->name));
        }
        // check if the subject is already in the workflow
        if (!$marking->getPlaces()) {
            if (!$this->definition->getInitialPlace()) {
                throw new LogicException(sprintf('The Marking is empty and there is no initial place for workflow "%s".', $this->name));
            }
            $marking->mark($this->definition->getInitialPlace());
            // update the subject with the new marking
            $this->markingStore->setMarking($subject, $marking);
        }
        // check that the subject has a known place
        $places = $this->definition->getPlaces();
        foreach ($marking->getPlaces() as $placeName => $nbToken) {
            if (!isset($places[ $placeName ])) {
                $message = sprintf('Place "%s" is not valid for workflow "%s".', $placeName, $this->name);
                if (!$places) {
                    $message .= ' It seems you forgot to add places to the current workflow.';
                }
                throw new LogicException($message);
            }
        }

        return $marking;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param object                                 $subject
     * @param \Symfony\Component\Workflow\Marking    $marking
     * @param \Symfony\Component\Workflow\Transition $transition
     *
     * @return bool|null
     */
    private function guardTransition($subject, Marking $marking, Transition $transition)
    {
        if (null === $this->dispatcher) {
            return null;
        }
        $event = new GuardEvent($subject, $marking, $transition, $this->name);
        $this->dispatcher->dispatch('flow.guard', $event);
        $this->dispatcher->dispatch(sprintf('flow.%s.guard', $this->name), $event);
        $this->dispatcher->dispatch(sprintf('flow.%s.guard.%s', $this->name, $transition->getName()), $event);

        return $event->isBlocked();
    }

    /**
     * @param                                        $subject
     * @param \Symfony\Component\Workflow\Transition $transition
     * @param \Symfony\Component\Workflow\Marking    $marking
     */
    private function leave($subject, Transition $transition, Marking $marking)
    {
        $places = $transition->getFroms();
        if (null !== $this->dispatcher) {
            $event = new Event($subject, $marking, $transition, $this->name);
            $this->dispatcher->dispatch('flow.leave', $event);
            $this->dispatcher->dispatch(sprintf('flow.%s.leave', $this->name), $event);
            foreach ($places as $place) {
                $this->dispatcher->dispatch(sprintf('flow.%s.leave.%s', $this->name, $place), $event);
            }
        }
        foreach ($places as $place) {
            $marking->unmark($place);
        }
    }

    /**
     * @param                                        $subject
     * @param \Symfony\Component\Workflow\Transition $transition
     * @param \Symfony\Component\Workflow\Marking    $marking
     */
    private function transition($subject, Transition $transition, Marking $marking)
    {
        if (null === $this->dispatcher) {
            return;
        }
        $event = new Event($subject, $marking, $transition, $this->name);
        $this->dispatcher->dispatch('flow.transition', $event);
        $this->dispatcher->dispatch(sprintf('flow.%s.transition', $this->name), $event);
        $this->dispatcher->dispatch(sprintf('flow.%s.transition.%s', $this->name, $transition->getName()), $event);
    }
}
