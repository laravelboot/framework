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
namespace LaravelBoot\Foundation\Passport;

use Carbon\Carbon;
use Illuminate\Events\Dispatcher;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider as LaravelPassportServiceProvider;
use LaravelBoot\Foundation\Passport\Listeners\RouterRegister;

/**
 * Class PassportServiceProvider.
 */
class PassportServiceProvider extends LaravelPassportServiceProvider
{
    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->app->make(Dispatcher::class)->subscribe(RouterRegister::class);
        $this->commands([
            ClientCommand::class,
            InstallCommand::class,
            KeysCommand::class,
        ]);
        Passport::tokensExpireIn(Carbon::now()->addHours(24));
    }

    /**
     * Register for service provider.
     */
    public function register()
    {
        Passport::cookie('laravelboot_token');
        $this->registerAuthorizationServer();
        $this->registerResourceServer();
        $this->registerGuard();
        $this->app->singleton('api', function ($app) {
            return new Passport($app, $app['events']);
        });
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuard()
    {
        $this->app['auth']->extend('passport', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }
}
