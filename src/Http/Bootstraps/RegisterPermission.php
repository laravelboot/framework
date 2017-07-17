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
namespace LaravelBoot\Foundation\Http\Bootstraps;

use Illuminate\Contracts\Foundation\Application;
use LaravelBoot\Foundation\Permission\Events\PermissionGroupRegister;
use LaravelBoot\Foundation\Permission\Events\PermissionModuleRegister;
use LaravelBoot\Foundation\Permission\Events\PermissionRegister;
use LaravelBoot\Foundation\Permission\Events\PermissionTypeRegister;

/**
 * Class RegisterPermission.
 */
class RegisterPermission
{
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        if ($application->isInstalled()) {
            $application->make('events')->dispatch(new PermissionModuleRegister($application['permission.module']));
            $application->make('events')->dispatch(new PermissionGroupRegister($application['permission']));
            $application->make('events')->dispatch(new PermissionRegister($application['permission']));
            $application->make('events')->dispatch(new PermissionTypeRegister($application['permission.type']));
        }
    }
}
