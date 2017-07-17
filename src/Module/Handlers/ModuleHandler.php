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
use LaravelBoot\Foundation\Module\Module;
use LaravelBoot\Foundation\Module\ModuleManager;
use LaravelBoot\Foundation\Routing\Abstracts\Handler;

/**
 * Class ModuleHandler.
 */
class ModuleHandler extends Handler
{
    /**
     * @var \LaravelBoot\Foundation\Module\ModuleManager
     */
    protected $manager;

    /**
     * ModuleHandler constructor.
     *
     * @param \Illuminate\Container\Container         $container
     * @param \LaravelBoot\Foundation\Module\ModuleManager $manager
     */
    public function __construct(Container $container, ModuleManager $manager)
    {
        parent::__construct($container);
        $this->manager = $manager;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->withCode(200)->withData($this->manager->getModules()->transform(function (Module $module) {
            return [
                'author' => $module->getAuthor(),
                'enabled' => $module->isEnabled(),
                'description' => $module->getDescription(),
                'identification' => $module->getIdentification(),
                'name' => $module->getName(),
            ];
        })->toArray())->withMessage('获取模块列表成功！');
    }
}
