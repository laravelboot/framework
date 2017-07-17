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
 * Class PermissionGroup.
 */
class PermissionGroup
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * PermissionGroup constructor.
     *
     * @param \Illuminate\Container\Container $container
     * @param array                           $attributes
     */
    public function __construct(Container $container, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->container = $container;
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
     * @return string
     */
    public function identification()
    {
        return $this->attributes['identification'];
    }

    /**
     * @return string
     */
    public function module()
    {
        return $this->attributes['module'];
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
            'module',
            'name',
        ];
        foreach ($needs as $need) {
            if (!isset($attributes[$need])) {
                return false;
            }
        }

        return true;
    }
}
