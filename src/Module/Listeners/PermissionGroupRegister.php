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
namespace LaravelBoot\Foundation\Module\Listeners;

use LaravelBoot\Foundation\Permission\Abstracts\PermissionGroupRegister as AbstractPermissionGroupRegister;

/**
 * Class PermissionGroupRegister.
 */
class PermissionGroupRegister extends AbstractPermissionGroupRegister
{
    /**
     * Handle Permission Group Register.
     */
    public function handle()
    {
        $this->manager->extend([
            'description'    => '模块权限定义。',
            'identification' => 'module',
            'module'         => 'global',
            'name'           => '模块权限',
        ]);
    }
}
