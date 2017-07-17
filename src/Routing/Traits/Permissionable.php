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
namespace LaravelBoot\Foundation\Routing\Traits;

/**
 * Trait Permissionable.
 */
trait Permissionable
{
    /**
     * Check for permission.
     *
     * @param $key
     *
     * @return bool
     */
    protected function permission($key)
    {
        return $this->container->make('permission')->check($key);
    }
}
