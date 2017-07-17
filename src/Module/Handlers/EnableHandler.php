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
namespace LaravelBoot\Foundation\Module\Handlers;

use Illuminate\Container\Container;
use LaravelBoot\Foundation\Routing\Abstracts\Handler;
use LaravelBoot\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class EnableHandler.
 */
class EnableHandler extends Handler
{
    /**
     * @var \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $settings;

    /**
     * EnableHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository $settings
     */
    public function __construct(Container $container, SettingsRepository $settings)
    {
        parent::__construct($container);
        $this->settings = $settings;
    }

    /**
     * Execute Handler.
     */
    public function execute()
    {
        if (!$this->request->input('name')) {
            $this->withCode(500)->withError('');
        } else {
            $this->settings->set('module.' . $this->request->input('name') . '.enabled', $this->request->input('value'));
            $this->withCode(200)->withMessage('修改设置成功！');
        }
    }
}
