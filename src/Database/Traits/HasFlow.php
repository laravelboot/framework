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
namespace LaravelBoot\Foundation\Database\Traits;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LaravelBoot\Foundation\Database\Model;
use LaravelBoot\Foundation\Permission\PermissionManager;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Trait HasFlow.
 */
trait HasFlow
{
    /**
     * @var string
     */
    protected $initialPlace;

    /**
     * @var string
     */
    protected $flowName = '';

    /**
     * @var array
     */
    protected $flowPlaces = [];

    /**
     * @var array
     */
    protected $flowTransitions = [];

    /**
     * @return \Symfony\Component\Workflow\Definition
     */
    public function buildFlow()
    {
        return new Definition($this->flowPlaces, $this->flowTransitions, $this->initialPlace);
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    abstract public function name();

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    abstract public function places();

    /**
     * Definition of transitions for flow.
     *
     * @return array
     */
    abstract public function transitions();

    /**
     * Announce a transition.
     */
    public function announceTransition()
    {
    }

    /**
     * Enter a place.
     *
     * @param \Symfony\Component\Workflow\Event\Event $event
     */
    public function enterPlace(Event $event)
    {
    }

    /**
     * Entered a place.
     */
    public function enteredPlace()
    {
    }

    /**
     * Guard a transition.
     *
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     */
    abstract public function guardTransition(GuardEvent $event);

    /**
     * Leave a place.
     */
    public function leavePlace()
    {
    }

    /**
     * Into a transition.
     */
    public function intoTransition()
    {
    }

    /**
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     * @param $permission
     */
    protected function blockTransition(GuardEvent $event,$permission)
    {
        if ($permission) {
            $event->setBlocked(false);
        } else {
            $event->setBlocked(true);
        }
    }

    /**
     * @return array
     */
    public function registerFlowEvents()
    {
        $collection = new Collection();
        $name = method_exists($this, 'name') ? $this->{'name'}() : 'unnamed';
        $places = method_exists($this, 'places') ? $this->{'places'}() : [];
        if (method_exists($this, 'transitions')) {
            $transitions = $this->{'transitions'}();
            $transitions = collect($transitions)->transform(function (Transition $transition) {
                return $transition->getName();
            })->toArray();
        } else {
            $transitions = [];
        }
        foreach ($places as $place) {
            $collection->put('flow.' . $name . '.enter', 'enterPlace');
            $collection->put('flow.' . $name . '.entered', 'enteredPlace');
            $collection->put('flow.' . $name . '.leave', 'leavePlace');
        }
        foreach ($transitions as $transition) {
            $collection->put('flow.' . $name . '.announce', 'announceTransition');
            $collection->put('flow.' . $name . '.guard', 'guardTransition');
            $collection->put('flow.' . $name . '.transition', 'intoTransition');
        }

        return $collection->toArray();
    }

    /**
     * @param $identification
     *
     * @param $group
     *
     * @return bool
     */
    protected function permission($identification, $group)
    {
        if ($group instanceof Model) {
            $group = $group->getAttribute('identification');
        } else if ($group instanceof Collection) {
            $group = $group->transform(function (Model $group) {
                return $group->getAttribute('identification');
            })->toArray();
        }
        foreach ((array)$group as $item) {
            if (Container::getInstance()->make(PermissionManager::class)->check($identification, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $place
     *
     * @return $this
     */
    public function setInitialPlace($place)
    {
        $this->initialPlace = $place;

        return $this;
    }

    /**
     * @param string $place
     *
     * @return $this
     */
    public function addFlowPlace($place)
    {
        if (!preg_match('{^[\w\d_-]+$}', $place)) {
            throw new InvalidArgumentException(sprintf('The place "%s" contains invalid characters.', $place));
        }
        if (!$this->flowPlaces) {
            $this->initialPlace = $place;
        }
        $this->flowPlaces[$place] = $place;

        return $this;
    }

    /**
     * @param array $places
     *
     * @return $this
     */
    public function addFlowPlaces(array $places)
    {
        foreach ($places as $place) {
            $this->addFlowPlace($place);
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Workflow\Transition $transition
     *
     * @return $this
     */
    public function addFlowTransition(Transition $transition)
    {
        $this->flowTransitions[] = $transition;

        return $this;
    }

    /**
     * @param array $transitions
     *
     * @return $this
     */
    public function addFlowTransitions(array $transitions)
    {
        foreach ($transitions as $transition) {
            $this->addFlowTransition($transition);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMarking()
    {
        return $this->getAttribute('flow_marking');
    }

    /**
     * @param mixed $marking
     */
    public function setMarking($marking)
    {
        $this->setAttribute('flow_marketing', $marking);
    }

    /**
     * @param $name
     */
    public function setFlowName($name)
    {
        $this->flowName = $name;
    }

    /**
     * @return string
     */
    public function getFlowName(): string
    {
        return $this->flowName;
    }
}
