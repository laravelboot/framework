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
namespace LaravelBoot\Foundation\Setting\Listeners;

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
            'description'    => '全局权限定义。',
            'identification' => 'global',
            'module'         => 'global',
            'name'           => '全局权限',
        ]);
    }
}
