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
namespace LaravelBoot\Foundation\Flow;

use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;

/**
 * Class FlowServiceProvider.
 */
class FlowServiceProvider extends ServiceProvider
{
    /**
     * Register service to provider.
     */
    public function register()
    {
        $this->app->singleton('flow', function ($app) {
            return new FlowManager($app, $app['events']);
        });
    }
}
