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
use LaravelBoot\Foundation\Module\ModuleManager;
use LaravelBoot\Foundation\Routing\Abstracts\Handler;

/**
 * Class UpdateHandler.
 */
class UpdateHandler extends Handler
{
    /**
     * @var \LaravelBoot\Foundation\Module\ModuleManager
     */
    protected $manager;

    /**
     * UpdateHandler constructor.
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
     */
    public function execute()
    {
        $module = $this->manager->get($this->request->input('name'));
        if ($module && method_exists($provider = $module->getEntry(), 'update') && call_user_func([
                $provider,
                'update',
            ])
        ) {
            $this->withCode(200)->withMessage('');
        } else {
            $this->withCode(500)->withError('');
        }
    }
}
