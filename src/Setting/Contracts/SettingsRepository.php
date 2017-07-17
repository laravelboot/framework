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
namespace LaravelBoot\Foundation\Setting\Contracts;

/**
 * Interface SettingsRepository.
 */
interface SettingsRepository
{
    /**
     * Get all settings.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * Delete a setting value.
     *
     * @param $keyLike
     */
    public function delete($keyLike);

    /**
     * Get a setting value by key.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Set a setting value from key and value.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value);
}
