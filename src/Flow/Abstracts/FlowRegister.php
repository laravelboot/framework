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
namespace LaravelBoot\Foundation\Flow\Abstracts;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use LaravelBoot\Foundation\Event\Abstracts\EventSubscriber;
use LaravelBoot\Foundation\Flow\Events\FlowRegister as FlowRegisterEvent;
use LaravelBoot\Foundation\Flow\FlowManager;

/**
 * Class FlowRegister.
 */
abstract class FlowRegister extends EventSubscriber
{
    /**
     * @var \LaravelBoot\Foundation\Flow\FlowManager
     */
    protected $flow;

    /**
     * FlowRegister constructor.
     *
     * @param \Illuminate\Container\Container     $container
     * @param \Illuminate\Events\Dispatcher       $events
     * @param \LaravelBoot\Foundation\Flow\FlowManager $flow
     */
    public function __construct(Container $container, Dispatcher $events, FlowManager $flow)
    {
        parent::__construct($container, $events);
        $this->flow = $flow;
    }

    /**
     * Name of event.
     *
     * @throws \Exception
     * @return string|object
     */
    protected function getEvent()
    {
        return FlowRegisterEvent::class;
    }

    /**
     * Register flow or flows.
     */
    abstract public function handle();
}
