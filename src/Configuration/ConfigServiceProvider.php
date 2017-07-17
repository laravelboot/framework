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
namespace LaravelBoot\Foundation\Configuration;

use LaravelBoot\Foundation\Configuration\Loaders\FileLoader;
use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;

/**
 * Class ConfigServiceProvider.
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Register for service provider.
     */
    public function register()
    {
        $this->app->singleton('config', function ($app) {
            return new Repository($this->getConfigLoader(), $app['env']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['config'];
    }

    /**
     * Get config loader.
     *
     * @return \LaravelBoot\Foundation\Configuration\Loaders\FileLoader
     */
    public function getConfigLoader()
    {
        return new FileLoader($this->app['files'], $this->app['path'] . DIRECTORY_SEPARATOR . 'config');
    }
}
