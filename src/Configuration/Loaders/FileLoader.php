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
namespace LaravelBoot\Foundation\Configuration\Loaders;

use Illuminate\Filesystem\Filesystem;
use LaravelBoot\Foundation\Configuration\Contracts\Loader as LoaderContract;

/**
 * Class FileLoader.
 */
class FileLoader implements LoaderContract
{
    /**
     * @var string
     */
    protected $defaultPath;

    /**
     * @var array
     */
    protected $exists = [];

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var array
     */
    protected $hints = [];

    /**
     * FileLoader constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param                                   $defaultPath
     */
    public function __construct(Filesystem $files, $defaultPath)
    {
        $this->defaultPath = $defaultPath;
        $this->files = $files;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param string $environment
     * @param string $package
     * @param string $group
     * @param array  $items
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function cascadePackage($environment, $package, $group, $items)
    {
        $path = $this->getPackagePath($package, $group);
        if ($this->files->exists($path)) {
            $items = array_merge($items, $this->getRequire($path));
        }
        $path = $this->getPackagePath($package, $group, $environment);
        if ($this->files->exists($path)) {
            $items = array_merge($items, $this->getRequire($path));
        }

        return $items;
    }

    /**
     * Determine if the given group exists.
     *
     * @param string $group
     * @param string $namespace
     *
     * @return bool
     */
    public function exists($group, $namespace = null)
    {
        $key = $group . $namespace;
        if (isset($this->exists[$key])) {
            return $this->exists[$key];
        }
        $path = $this->getPath($namespace);
        if (is_null($path)) {
            return $this->exists[$key] = false;
        }
        $file = "{$path}/{$group}.php";
        $exists = $this->files->exists($file);

        return $this->exists[$key] = $exists;
    }

    /**
     * Returns all registered namespaces with the config loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->hints;
    }

    /**
     * Get the package path for an environment and group.
     *
     * @param string $env
     * @param string $package
     * @param string $group
     *
     * @return string
     */
    protected function getPackagePath($package, $group, $env = null)
    {
        $package = strtolower(str_replace('.', '/', $package));
        if (!$env) {
            $file = "{$package}/{$group}.php";
        } else {
            $file = "{$package}/{$env}/{$group}.php";
        }

        return $this->defaultPath . '/' . $file;
    }

    /**
     * Get the configuration path for a namespace.
     *
     * @param string $namespace
     *
     * @return string
     */
    protected function getPath($namespace)
    {
        if (is_null($namespace)) {
            return $this->defaultPath;
        } elseif (isset($this->hints[$namespace])) {
            return $this->hints[$namespace];
        }
    }

    /**
     * Get a file's contents by requiring it.
     *
     * @param  string $path
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getRequire($path)
    {
        return $this->files->getRequire($path);
    }

    /**
     * Load the given configuration group.
     *
     * @param string $environment
     * @param string $group
     * @param string $namespace
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function load($environment, $group, $namespace = null)
    {
        $items = [];
        $path = $this->getPath($namespace);
        if (is_null($path)) {
            return $items;
        }
        $file = "{$path}/{$group}.php";
        if ($this->files->exists($file)) {
            $items = $this->getRequire($file);
        }
        $file = "{$path}/{$environment}/{$group}.php";
        if ($this->files->exists($file)) {
            $items = $this->mergeEnvironment($items, $file);
        }

        return $items;
    }

    /**
     * Merge the items in the given file into the items.
     *
     * @param array  $items
     * @param string $file
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function mergeEnvironment(array $items, $file)
    {
        return array_replace_recursive($items, $this->getRequire($file));
    }
}
