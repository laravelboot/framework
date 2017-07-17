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
 * Class PermissionType.
 */
class PermissionType
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var \Illuminate\Container\Container
     */
    private $container;

    /**
     * PermissionType constructor.
     *
     * @param \Illuminate\Container\Container $container
     * @param array                           $attributes
     */
    public function __construct(Container $container, array $attributes = [])
    {
        $this->container = $container;
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return static
     */
    public static function createFromAttributes(array $attributes)
    {
        return new static(Container::getInstance(), $attributes);
    }

    /**
     * @return string
     */
    public function description()
    {
        return $this->attributes['description'];
    }

    /**
     * @return array
     */
    public function has()
    {
        $data = new Collection();
        $settings = $this->container->make('setting')->get('permissions', '');
        if ($settings) {
            $settings = collect(json_decode($settings));
        } else {
            $settings = collect();
        }
        $this->container->make(PermissionModuleManager::class)
            ->list()
            ->each(function (PermissionModule $module) use ($data, $settings) {
                $this->container->make(PermissionGroupManager::class)
                    ->groupsForModule($module->identification())
                    ->each(function (PermissionGroup $group) use ($data, $module, $settings) {
                        $this->container
                            ->make(PermissionManager::class)
                            ->permissionsForGroup($module->identification() . '::' . $group->identification())
                            ->each(function (Permission $permission) use ($data, $group, $module, $settings) {
                                $identification = $this->identification()
                                    . '::'
                                    . $module->identification()
                                    . '::'
                                    . $group->identification()
                                    . '::'
                                    . $permission->identification();
                                $data->put($identification, $settings->get($identification, []));
                            });
                    });
            });

        return $data->toArray();
    }

    /**
     * @return string
     */
    public function identification()
    {
        return $this->attributes['identification'];
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->attributes['name'];
    }

    /**
     * @param array $attributes
     *
     * @return bool
     */
    public static function validate(array $attributes)
    {
        $needs = [
            'description',
            'identification',
            'name',
        ];
        foreach ($needs as $need) {
            if (!isset($attributes[ $need ])) {
                return false;
            }
        }

        return true;
    }
}
