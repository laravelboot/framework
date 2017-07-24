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
namespace LaravelBoot\Foundation\Module;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;
use LaravelBoot\Foundation\Module\Commands\GenerateCommand;
use LaravelBoot\Foundation\Module\Commands\ListCommand;
use LaravelBoot\Foundation\Module\Commands\ListUnloadedCommand;
use LaravelBoot\Foundation\Module\Commands\OpCommand;
use LaravelBoot\Foundation\Module\Listeners\CsrfTokenRegister;
use LaravelBoot\Foundation\Module\Listeners\PermissionGroupRegister;
use LaravelBoot\Foundation\Module\Listeners\PermissionRegister;
use LaravelBoot\Foundation\Module\Listeners\RouteRegister;

/**
 * Class ModuleServiceProvider.
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * ModuleServiceProvider constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->files = $app->make(Filesystem::class);
    }

    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->app->make(Dispatcher::class)->subscribe(CsrfTokenRegister::class);
        $this->app->make(Dispatcher::class)->subscribe(PermissionGroupRegister::class);
        $this->app->make(Dispatcher::class)->subscribe(PermissionRegister::class);
        $this->app->make(ModuleManager::class)->getEnabledModules()->each(function (Module $module) {
            $path = $module->getDirectory();
            if ($this->files->isDirectory($path) && is_string($module->getEntry())) {
                $this->app->register($module->getEntry());
            }
        });
        $this->commands([
            GenerateCommand::class,
            ListCommand::class,
            ListUnloadedCommand::class,
            OpCommand::class
        ]);
    }

    /**
     * Register for service provider.
     */
    public function register()
    {
        $this->app->singleton('module', function ($app) {
            return new ModuleManager($app, $app['config'], $app['events'], $app['files']);
        });
    }
}
