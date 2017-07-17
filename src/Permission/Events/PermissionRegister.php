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

use LaravelBoot\Foundation\Permission\PermissionManager;

/**
 * Class PermissionRegister.
 */
class PermissionRegister
{
    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionManager
     */
    protected $permission;

    /**
     * PermissionRegister constructor.
     *
     * @param \LaravelBoot\Foundation\Permission\PermissionManager $permission
     *
     * @internal param \Illuminate\Container\Container $container
     */
    public function __construct(PermissionManager $permission)
    {
        $this->permission = $permission;
    }
}
