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

use LaravelBoot\Foundation\Permission\PermissionModuleManager;

/**
 * Class PermissionModuleRegister.
 */
class PermissionModuleRegister
{
    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionModuleManager
     */
    protected $module;

    /**
     * PermissionModuleRegister constructor.
     *
     * @param \LaravelBoot\Foundation\Permission\PermissionModuleManager $module
     *
     * @internal param \Illuminate\Container\Container $container
     */
    public function __construct(PermissionModuleManager $module)
    {
        $this->module = $module;
    }
}
