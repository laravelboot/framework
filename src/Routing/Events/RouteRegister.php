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
namespace LaravelBoot\Foundation\Routing\Events;

use Illuminate\Routing\Router;

/**
 * Class RouteRegister.
 */
class RouteRegister
{
    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * RouteRegister constructor.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @internal param \Illuminate\Container\Container|\Illuminate\Contracts\Foundation\Application $container
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
}
