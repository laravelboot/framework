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
namespace LaravelBoot\Foundation\Setting;

use LaravelBoot\Foundation\Setting\Contracts\SettingsRepository as SettingsRepositoryContract;

/**
 * Class MemoryCacheSettingsRepository.
 */
class MemoryCacheSettingsRepository implements SettingsRepositoryContract
{
    /**
     * @var \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $inner;

    /**
     * @var bool
     */
    protected $isCached;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * MemoryCacheSettingsRepository constructor.
     *
     * @param \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository $inner
     */
    public function __construct(SettingsRepositoryContract $inner)
    {
        $this->inner = $inner;
    }

    /**
     * Get all settings.
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function all()
    {
        if (!$this->isCached) {
            $this->cache = $this->inner->all();
            $this->isCached = true;
        }

        return $this->cache;
    }

    /**
     * Delete a setting value.
     *
     * @param $key
     */
    public function delete($key)
    {
        unset($this->cache[$key]);
        $this->inner->delete($key);
    }

    /**
     * Get a setting value by key.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        } else {
            return array_get($this->all(), $key, $default);
        }
    }

    /**
     * Set a setting value from key and value.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->cache[$key] = $value;
        $this->inner->set($key, $value);
    }
}
