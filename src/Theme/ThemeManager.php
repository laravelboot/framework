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

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class ThemeManager.
 */
class ThemeManager
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
    protected $themes;

    /**
     * ThemeManager constructor.
     *
     * @param \Illuminate\Container\Container   $container
     * @param \Illuminate\Events\Dispatcher     $events
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Container $container, Dispatcher $events, Filesystem $files)
    {
        $this->container = $container;
        $this->events = $events;
        $this->files = $files;
        $this->themes = new Collection();
    }

    /**
     * Themes of installed or not installed.
     *
     * @param bool $installed
     *
     * @return \Illuminate\Support\Collection
     */
    public function getThemes($installed = false)
    {
        if ($this->themes->isEmpty()) {
            if ($this->files->isDirectory($this->getThemePath()) && !empty($directories = $this->files->directories($this->getThemePath()))) {
                (new Collection($directories))->each(function ($directory) use ($installed) {
                    if ($this->files->exists($file = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {
                        $package = new Collection(json_decode($this->files->get($file), true));
                        if (Arr::get($package, 'type') == 'laravelboot-extension' && $name = Arr::get($package, 'name')) {
                            $module = new Theme($name, Arr::get($package, 'authors'),
                                Arr::get($package, 'description'));
                            if ($installed) {
                                $module->setInstalled($installed);
                            }
                            $this->themes->put($directory, $module);
                        }
                    }
                });
            }
        }
        return $this->themes;
    }

    /**
     * @return string
     */
    public function getThemePath()
    {
        return $this->container->basePath() . DIRECTORY_SEPARATOR . 'themes';
    }
}
