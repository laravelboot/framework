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

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Configuration\Repository as ConfigurationRepository;

/**
 * Class ModuleManager.
 */
class ModuleManager
{
    /**
     * Container instance.
     *
     * @var \Illuminate\Container\Container|\LaravelBoot\Foundation\Application
     */
    protected $container;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $modules;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $unloaded;

    /**
     * @var \LaravelBoot\Foundation\Configuration\Repository
     */
    private $configuration;

    /**
     * ModuleManager constructor.
     *
     * @param \Illuminate\Container\Container             $container
     * @param \LaravelBoot\Foundation\Configuration\Repository $configuration
     * @param \Illuminate\Events\Dispatcher               $events
     * @param \Illuminate\Filesystem\Filesystem           $files
     */
    public function __construct(Container $container, ConfigurationRepository $configuration, Dispatcher $events, Filesystem $files)
    {
        $this->configuration = $configuration;
        $this->container = $container;
        $this->events = $events;
        $this->files = $files;
        $this->modules = new Collection();
        $this->unloaded = new Collection();
    }

    /**
     * Get a module by name.
     *
     * @param $name
     *
     * @return \LaravelBoot\Foundation\Module\Module
     */
    public function get($name)
    {
        return $this->modules->get($name);
    }

    /**
     * Modules of enabled.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEnabledModules()
    {
        $list = new Collection();
        if ($this->getModules()->isNotEmpty()) {
            $this->getModules()->each(function (Module $module) use ($list) {
                $module->isEnabled() && $list->put($module->getIdentification(), $module);
            });
        }

        return $list;
    }

    /**
     * Modules of installed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInstalledModules()
    {
        $list = new Collection();
        if ($this->getModules()->isNotEmpty()) {
            $this->modules->each(function (Module $module) use ($list) {
                $module->isInstalled() && $list->put($module->getIdentification(), $module);
            });
        }

        return $list;
    }

    /**
     * Modules of list.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getModules()
    {
        if ($this->modules->isEmpty()) {
            if ($this->files->isDirectory($this->getModulePath())) {
                collect($this->files->directories($this->getModulePath()))->each(function ($directory) {
                    if ($this->files->exists($file = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {
                        $package = new Collection(json_decode($this->files->get($file), true));
                        $identification = Arr::get($package, 'name');
                        $type = Arr::get($package, 'type');
                        if ($type == 'laravelboot-module' && $identification) {
                            $provider = '';
                            if ($entries = data_get($package, 'autoload.psr-4')) {
                                foreach ($entries as $namespace => $entry) {
                                    $provider = $namespace . 'ModuleServiceProvider';
                                }
                            }
                            if ($this->files->exists($autoload = $directory . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
                                $this->files->requireOnce($autoload);
                            }
                            $authors = Arr::get($package, 'authors');
                            $description = Arr::get($package, 'description');
                            if (class_exists($provider)) {
                                $module = new Module($identification);
                                $module->setAuthor($authors);
                                $module->setDescription($description);
                                $module->setDirectory($directory);
                                $module->setEnabled($this->container->isInstalled() ? $this->container->make('config')->get('module.' . $identification . '.enabled', false) : false);
                                $module->setInstalled($this->container->isInstalled() ? $this->container->make('setting')->get('module.' . $identification . '.installed', false) : false);
                                $module->setEntry($provider);
                                if (method_exists($provider, 'alias')) {
                                    $module->setAlias(call_user_func([$provider, 'alias']));
                                } else {
                                    $module->setAlias([$identification]);
                                }
                                method_exists($provider, 'description') && $module->setDescription(call_user_func([$provider, 'description']));
                                method_exists($provider, 'name') && $module->setName(call_user_func([$provider, 'name']));
                                method_exists($provider, 'script') && $module->setScript(call_user_func([$provider, 'script']));
                                method_exists($provider, 'stylesheet') && $module->setStylesheet(call_user_func([$provider, 'stylesheet']));
                                method_exists($provider, 'version') && $module->setVersion(call_user_func([$provider, 'version']));
                                $this->modules->put($identification, $module);
                            } else {
                                $this->unloaded->put($identification, [
                                    'authors'        => $authors,
                                    'description'    => $description,
                                    'directory'      => $directory,
                                    'identification' => $identification,
                                    'provider'       => $provider,
                                ]);
                            }
                        }
                    }
                });
            }
        }

        return $this->modules;
    }

    /**
     * Modules of not-installed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNotInstalledModules()
    {
        $list = new Collection();
        if ($this->getModules()->isNotEmpty()) {
            $this->modules->each(function (Module $module) use ($list) {
                $module->isInstalled() || $list->put($module->getIdentification(), $module);
            });
        }

        return $list;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUnloadedModules()
    {
        return $this->unloaded;
    }

    /**
     * Check for module exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->modules->has($name);
    }

    /**
     * Module path.
     *
     * @return string
     */
    public function getModulePath()
    {
        return $this->container->basePath() . DIRECTORY_SEPARATOR . $this->configuration->get('module.directory');
    }
}
