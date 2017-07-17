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
namespace LaravelBoot\Foundation\Permission\Events;

use LaravelBoot\Foundation\Permission\PermissionTypeManager;

/**
 * Class PermissionTypeRegister.
 */
class PermissionTypeRegister
{
    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionTypeManager
     */
    private $type;

    /**
     * PermissionRegister constructor.
     *
     * @param \LaravelBoot\Foundation\Permission\PermissionTypeManager $type
     *
     * @internal param \Illuminate\Container\Container $container
     */
    public function __construct(PermissionTypeManager $type)
    {
        $this->type = $type;
    }
}
