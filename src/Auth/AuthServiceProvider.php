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
namespace LaravelBoot\Foundation\Auth;

use Illuminate\Auth\AuthServiceProvider as IlluminateAuthServiceProvider;
use Illuminate\Events\Dispatcher;
use LaravelBoot\Foundation\Auth\Listeners\RouteRegister;

/**
 * Class AuthServiceProvider.
 */
class AuthServiceProvider extends IlluminateAuthServiceProvider
{
    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->app->make(Dispatcher::class)->subscribe(RouteRegister::class);
    }
}
