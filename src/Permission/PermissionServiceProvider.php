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
namespace LaravelBoot\Foundation\Permission;

use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;
use LaravelBoot\Foundation\Permission\Commands\PermissionCommand;

/**
 * Class PermissionServiceProvider.
 */
class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Boot service.
     */
    public function boot()
    {
        $this->commands(PermissionCommand::class);
    }

    /**
     * ServiceProvider register.
     */
    public function register()
    {
        $this->app->singleton('permission', function ($app) {
            return new PermissionManager($app, $app['permission.group']);
        });
        $this->app->singleton('permission.group', function ($app) {
            return new PermissionGroupManager($app, $app['permission.module']);
        });
        $this->app->singleton('permission.module', function ($app) {
            return new PermissionModuleManager($app);
        });
        $this->app->singleton('permission.type', function ($app) {
            return new PermissionTypeManager($app);
        });
    }
}
