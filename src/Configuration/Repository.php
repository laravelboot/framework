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

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use LaravelBoot\Foundation\Configuration\Contracts\Loader;
use LaravelBoot\Foundation\Configuration\Traits\KeyParser;

/**
 * Class Repository.
 */
class Repository implements ArrayAccess, ConfigContract
{
    use KeyParser;

    /**
     * @var \LaravelBoot\Foundation\Configuration\Contracts\Loader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $packages = [];

    /**
     * @var array
     */
    protected $afterLoad = [];

    /**
     * Repository constructor.
     *
     * @param \LaravelBoot\Foundation\Configuration\Contracts\Loader $loader
     * @param string                                            $environment
     */
    public function __construct(Loader $loader, $environment)
    {
        $this->environment = $environment;
        $this->loader = $loader;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get a configuration option.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set a configuration option.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $default = microtime(true);

        return $this->get($key, $default) !== $default;
    }

    /**
     * Determine if a configuration group exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasGroup($key)
    {
        list($namespace, $group, $item) = $this->parseConfigKey($key);

        return $this->loader->exists($group, $namespace);
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($namespace, $group, $item) = $this->parseConfigKey($key);
        $collection = $this->getCollection($group, $namespace);
        $this->load($group, $namespace, $collection);

        return array_get($this->items[$collection], $item, $default);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Call the after load callback for a namespace.
     *
     * @param string $namespace
     * @param string $group
     * @param array  $items
     *
     * @return array
     */
    protected function callAfterLoad($namespace, $group, $items)
    {
        $callback = $this->afterLoad[$namespace];

        return call_user_func($callback, $this, $group, $items);
    }

    /**
     * Get the collection identifier.
     *
     * @param string $group
     * @param string $namespace
     *
     * @return string
     */
    protected function getCollection($group, $namespace = null)
    {
        $namespace = $namespace ?: '*';

        return $namespace . '::' . $group;
    }

    /**
     * Set the loader implementation.
     *
     * @param array|string $key
     * @param mixed        $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                $this->set($innerKey, $innerValue);
            }
        } else {
            list($namespace, $group, $item) = $this->parseConfigKey($key);
            $collection = $this->getCollection($group, $namespace);
            $this->load($group, $namespace, $collection);
            if (is_null($item)) {
                $this->items[$collection] = $value;
            } else {
                array_set($this->items[$collection], $item, $value);
            }
        }
    }

    /**
     * Parse an array of namespaced segments.
     *
     * @param string $key
     *
     * @return array
     */
    protected function parseNamespacedSegments($key)
    {
        list($namespace, $item) = explode('::', $key);
        if (in_array($namespace, $this->packages)) {
            return $this->parsePackageSegments($key, $namespace, $item);
        }

        return $this->keyParserParseSegments($key);
    }

    /**
     * Parse the segments of a package namespace.
     *
     * @param string $key
     * @param string $namespace
     * @param string $item
     *
     * @return array
     */
    protected function parsePackageSegments($key, $namespace, $item)
    {
        $itemSegments = explode('.', $item);
        if (!$this->loader->exists($itemSegments[0], $namespace)) {
            return [$namespace, 'config', $item];
        }

        return $this->keyParserParseSegments($key);
    }

    /**
     * Load the configuration group for the key.
     *
     * @param string $group
     * @param string $namespace
     * @param string $collection
     *
     * @return void
     */
    protected function load($group, $namespace, $collection)
    {
        $env = $this->environment;
        if (isset($this->items[$collection])) {
            return;
        }
        $items = $this->loader->load($env, $group, $namespace);
        if (isset($this->afterLoad[$namespace])) {
            $items = $this->callAfterLoad($namespace, $group, $items);
        }
        $this->items[$collection] = $items;
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param string $key
     *
     * @return array
     */
    public function parseConfigKey($key)
    {
        if (strpos($key, '::') === false) {
            return $this->parseKey($key);
        }
        if (isset($this->keyParserCache[$key])) {
            return $this->keyParserCache[$key];
        }
        $segments = explode('.', $key);
        $parsed = $this->parseNamespacedSegments($key);

        return $this->keyParserCache[$key] = $parsed;
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);
        array_unshift($array, $value);
        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);
        $array[] = $value;
        $this->set($key, $array);
    }
}
