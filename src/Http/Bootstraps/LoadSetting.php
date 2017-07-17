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
namespace LaravelBoot\Foundation\Http\Bootstraps;

use Illuminate\Contracts\Foundation\Application;
use LaravelBoot\Foundation\Configuration\Repository as ConfigRepository;
use LaravelBoot\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class LoadSetting.
 */
class LoadSetting
{
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        if ($application->isInstalled()) {
            $config = $application->make(ConfigRepository::class);
            $setting = $application->make(SettingsRepository::class);
            date_default_timezone_set($setting->get('setting.timezone', $config['app.timezone']));
            $config->set('app.debug', $setting->get('setting.debug', true));
            $config->set('mail.driver', $setting->get('mail.driver', 'smtp'));
            $config->set('mail.host', $setting->get('mail.host'));
            $config->set('mail.port', $setting->get('mail.port'));
            $config->set('mail.from.address', $setting->get('mail.from'));
            $config->set('mail.from.name', $setting->get('site.title', 'Notadd'));
            $config->set('mail.encryption', $setting->get('mail.encryption'));
            $config->set('mail.username', $setting->get('mail.username'));
            $config->set('mail.password', $setting->get('mail.password'));
        }
    }
}
