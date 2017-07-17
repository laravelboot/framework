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
 * Class AllHandler.
 */
class AllHandler extends Handler
{
    /**
     * @var \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $settings;

    /**
     * AllHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository $settings
     */
    public function __construct(Container $container, SettingsRepository $settings) {
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
        $this->withCode(200)->withData($this->settings->all()->toArray())->withMessage('获取全局设置成功！');
    }
}
