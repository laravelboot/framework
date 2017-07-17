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
namespace LaravelBoot\Foundation\Routing\Abstracts;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use LaravelBoot\Foundation\Event\Abstracts\EventSubscriber;
use LaravelBoot\Foundation\Routing\Events\RouteRegister as RouteRegisterEvent;

/**
 * Class AbstractRouteRegister.
 */
abstract class RouteRegister extends EventSubscriber
{
    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * RouteRegister constructor.
     *
     * @param \Illuminate\Container\Container $container
     * @param \Illuminate\Events\Dispatcher   $events
     * @param \Illuminate\Routing\Router      $request
     */
    public function __construct(Container $container, Dispatcher $events, Router $request)
    {
        parent::__construct($container, $events);
        $this->router = $request;
    }

    /**
     * Name of event.
     *
     * @return mixed
     */
    protected function getEvent()
    {
        return RouteRegisterEvent::class;
    }

    /**
     * Handle Route Register.
     */
    abstract public function handle();
}
