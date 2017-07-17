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
namespace LaravelBoot\Foundation\Setting\Handlers;

use Illuminate\Container\Container;
use LaravelBoot\Foundation\Routing\Abstracts\Handler;
use LaravelBoot\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class GetHandler.
 */
class GetHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $settings;

    /**
     * GetHandler constructor.
     *
     * @param Container $container
     * @param SettingsRepository $settings
     */
    public function __construct(Container $container, SettingsRepository $settings)
    {
        parent::__construct($container);
        $this->settings = $settings;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->withCode(200)->withData([
            'beian' => $this->settings->get('site.beian', ''),
            'company' => $this->settings->get('site.company', ''),
            'copyright' => $this->settings->get('site.copyright', ''),
            'domain' => $this->settings->get('site.domain', ''),
            'enabled' => $this->settings->get('site.enabled', true),
            'name' => $this->settings->get('site.name', ''),
            'statistics' => $this->settings->get('site.statistics', ''),
        ])->withMessage('获取配置项成功！');
    }
}
