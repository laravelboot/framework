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

use LaravelBoot\Foundation\Permission\Abstracts\PermissionRegister as AbstractPermissionRegister;

/**
 * Class PermissionRegister.
 */
class PermissionRegister extends AbstractPermissionRegister
{
    /**
     * Handle Permission Register.
     */
    public function handle()
    {
        $this->manager->extend([
            'default'        => false,
            'description'    => '获取全局配置项',
            'group'          => 'global',
            'identification' => 'setting.get',
            'module'         => 'global',
        ]);
        $this->manager->extend([
            'default'        => false,
            'description'    => '设置全局配置项',
            'group'          => 'global',
            'identification' => 'setting.set',
            'module'         => 'global',
        ]);
    }
}
