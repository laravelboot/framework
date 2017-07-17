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

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Transition;

/**
 * Class FlowBuilder.
 */
class FlowBuilder
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var
     */
    protected $initialPlace;

    /**
     * @var
     */
    protected $marking;

    /**
     * @var string
     */
    protected $name = 'unnamed';

    /**
     * @var array
     */
    protected $places = [];

    /**
     * @var array
     */
    protected $transitions = [];

    /**
     * @return Definition
     */
    public function build()
    {
        return new Definition($this->places, $this->transitions, $this->initialPlace);
    }

    /**
     * Clear all data in the builder.
     *
     * @return $this
     */
    public function reset()
    {
        $this->places = [];
        $this->transitions = [];
        $this->initialPlace = null;

        return $this;
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
    public function addPlace($place)
    {
        if (!preg_match('{^[\w\d_-]+$}', $place)) {
            throw new InvalidArgumentException(sprintf('The place "%s" contains invalid characters.', $place));
        }
        if (!$this->places) {
            $this->initialPlace = $place;
        }
        $this->places[ $place ] = $place;

        return $this;
    }

    /**
     * @param array $places
     *
     * @return $this
     */
    public function addPlaces(array $places)
    {
        foreach ($places as $place) {
            $this->addPlace($place);
        }

        return $this;
    }

    /**
     * @param array $transitions
     *
     * @return $this
     */
    public function addTransitions(array $transitions)
    {
        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Workflow\Transition $transition
     *
     * @return $this
     */
    public function addTransition(Transition $transition)
    {
        $this->transitions[] = $transition;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function getMarking()
    {
        return $this->marking;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param mixed $marking
     */
    public function setMarking($marking)
    {
        $this->marking = $marking;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
