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
namespace LaravelBoot\Foundation\Auth\Listeners;

use LaravelBoot\Foundation\Auth\Controllers\AuthController;
use LaravelBoot\Foundation\Routing\Abstracts\RouteRegister as AbstractRouteRegister;

/**
 * Class RouteRegister.
 */
class RouteRegister extends AbstractRouteRegister
{
    /**
     * Handle Route Register.
     */
    public function handle()
    {
        $this->router->group(['middleware' => 'web'], function () {
            $this->router->get($this->container->bound('auth.logout') ? $this->container->make('auth.logout') : 'logout',
                $this->container->bound('auth.logout.resolver') ? $this->container->make('auth.logout.resolver') : AuthController::class . '@logout');
            $this->router->resource($this->container->bound('auth.login') ? $this->container->make('auth.login') : 'login',
                $this->container->bound('auth.logout.resolver') ? $this->container->make('auth.logout.resolver') : AuthController::class);
        });
    }
}
