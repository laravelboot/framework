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
use LaravelBoot\Foundation\Routing\Events\RouteRegister;

/**
 * Class RegisterRouter.
 */
class RegisterRouter
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
            if ($application->routesAreCached()) {
                $application->booted(function () use ($application) {
                    require $application->getCachedRoutesPath();
                });
            } else {
                $application->make('events')->dispatch(new RouteRegister($application['router']));
                $application->booted(function () use ($application) {
                    $application['router']->getRoutes()->refreshNameLookups();
                });
            }
        }
    }
}
