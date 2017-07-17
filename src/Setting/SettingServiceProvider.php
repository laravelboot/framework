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
namespace LaravelBoot\Foundation\Setting;

use Illuminate\Events\Dispatcher;
use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;
use LaravelBoot\Foundation\Setting\Listeners\CsrfTokenRegister;
use LaravelBoot\Foundation\Setting\Listeners\PermissionGroupRegister;
use LaravelBoot\Foundation\Setting\Listeners\PermissionRegister;
use LaravelBoot\Foundation\Setting\Listeners\RouteRegister;

/**
 * Class SettingServiceProvider.
 */
class SettingServiceProvider extends ServiceProvider
{
    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->app->make(Dispatcher::class)->subscribe(CsrfTokenRegister::class);
        $this->app->make(Dispatcher::class)->subscribe(PermissionGroupRegister::class);
        $this->app->make(Dispatcher::class)->subscribe(PermissionRegister::class);
        $this->app->make(Dispatcher::class)->subscribe(RouteRegister::class);
    }

    /**
     * Register for service provider.
     */
    public function register()
    {
        $this->app->singleton('setting', function () {
            return new MemoryCacheSettingsRepository(new DatabaseSettingsRepository($this->app->make('db.connection')));
        });
    }
}
