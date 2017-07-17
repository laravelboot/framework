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
use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Module\Abstracts\Uninstaller;
use LaravelBoot\Foundation\Module\ModuleManager;
use LaravelBoot\Foundation\Routing\Abstracts\Handler;

/**
 * Class UninstallHandler.
 */
class UninstallHandler extends Handler
{
    /**
     * @var \LaravelBoot\Foundation\Module\ModuleManager
     */
    protected $manager;

    /**
     * UninstallHandler constructor.
     *
     * @param \Illuminate\Container\Container $container
     * @param \LaravelBoot\Foundation\Module\ModuleManager $manager
     */
    public function __construct(Container $container, ModuleManager $manager)
    {
        parent::__construct($container);
        $this->manager = $manager;
    }

    /**
     * Execute Handler.
     */
    public function execute()
    {
        set_time_limit(0);
        $result = false;
        $module = $this->manager->get($this->request->input('identification'));
        if ($module && method_exists($provider = $module->getEntry(), 'uninstall')) {
            if (($uninstaller = $this->container->make(call_user_func([$provider, 'uninstall']))) instanceof Uninstaller) {
                $uninstaller->setModule($module);
                if ($uninstaller->uninstall()) {
                    $result = true;
                } else {
                    $this->code = 500;
                }
                $this->parseInfo($uninstaller->info());
                $this->container->make('log')->info('uninstall data:', $this->data());
            }
        }
        if ($result) {
            $this->withCode(200)->withMessage('');
        } else {
            $this->withCode(500)->withError('');
        }
    }

    /**
     * @param \Illuminate\Support\Collection $data
     */
    protected function parseInfo(Collection $data) {
        $data->has('data') && $this->data = collect($data->get('data'));
        $data->has('errors') && $this->errors = collect($data->get('errors'));
        $data->has('messages') && $this->messages = collect($data->get('messages'));
    }
}
