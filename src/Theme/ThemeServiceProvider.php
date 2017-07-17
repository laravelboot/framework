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
namespace LaravelBoot\Foundation\Theme;

use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;

/**
 * Class ThemeServiceProvider.
 */
class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Boot service provider.
     */
    public function boot()
    {
    }

    /**
     * Register service provider.
     */
    public function register()
    {
        $this->app->singleton('theme', function ($app) {
            return new ThemeManager($app, $app['events'], $app['files']);
        });
    }
}
