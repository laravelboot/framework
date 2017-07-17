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
namespace LaravelBoot\Foundation\Permission;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * Class PermissionManager.
 */
class PermissionManager
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionGroupManager
     */
    protected $group;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $permissions;

    /**
     * PermissionManager constructor.
     *
     * @param \Illuminate\Container\Container                      $container
     * @param \LaravelBoot\Foundation\Permission\PermissionGroupManager $group
     */
    public function __construct(Container $container, PermissionGroupManager $group)
    {
        $this->container = $container;
        $this->group = $group;
        $this->permissions = new Collection();
    }

    /**
     * @param $identification
     * @param $group
     *
     * @return bool
     */
    public function check($identification, $group)
    {
        if (!$identification || !$group) {
            return false;
        }
        $permissions = json_decode($this->container->make('setting')->get('permissions', json_encode([])), true);
        if (array_key_exists($identification, $permissions)) {
            $groups = $permissions[$identification];
            if (in_array($group, $groups)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $attributes
     *
     * @return \Illuminate\Support\Collection|bool
     */
    public function extend(array $attributes)
    {
        $group = $attributes['module'] . '::' . $attributes['group'];
        $permission = $attributes['module'] . '::' . $attributes['group'] . '::' . $attributes['identification'];
        if (Permission::validate($attributes) && $this->group->exists($group) && !$this->permissions->has($permission)) {
            $this->permissions->put($permission, Permission::createFromAttributes($attributes));

            return true;
        }

        return false;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function permissions()
    {
        return $this->permissions;
    }

    /**
     * @param $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function permissionsForGroup($key)
    {
        list($module, $group) = explode('::', $key);
        return $this->permissions->filter(function (Permission $permission) use ($group, $module) {
            return $permission->module() == $module && $permission->group() == $group;
        });
    }
}
