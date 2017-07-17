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
namespace LaravelBoot\Foundation\Routing;

use Illuminate\Routing\RoutingServiceProvider as IlluminateRoutingServiceProvider;

/**
 * Class RoutingServiceProvider.
 */
class RoutingServiceProvider extends IlluminateRoutingServiceProvider
{
    /**
     * Register the Redirector service.
     */
    protected function registerRedirector()
    {
        $this->app->singleton('redirect', function ($app) {
            $redirector = new Redirector($app['url']);
            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });
    }
}
