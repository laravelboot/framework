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
 * Trait Settingable.
 */
trait Settingable
{
    /**
     * Get setting instance.
     *
     * @return \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository
     */
    protected function setting()
    {
        return $this->container->make('setting');
    }
}
