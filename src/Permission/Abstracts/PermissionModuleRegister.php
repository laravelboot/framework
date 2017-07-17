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
namespace LaravelBoot\Foundation\Permission\Abstracts;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use LaravelBoot\Foundation\Event\Abstracts\EventSubscriber;
use LaravelBoot\Foundation\Permission\Events\PermissionModuleRegister as PermissionModuleRegisterEvent;
use LaravelBoot\Foundation\Permission\PermissionModuleManager;

/**
 * Class PermissionModuleRegister.
 */
abstract class PermissionModuleRegister extends EventSubscriber
{
    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionModuleManager
     */
    protected $manager;

    /**
     * PermissionModuleRegister constructor.
     *
     * @param \Illuminate\Container\Container                       $container
     * @param \Illuminate\Events\Dispatcher                         $events
     * @param \LaravelBoot\Foundation\Permission\PermissionModuleManager $manager
     */
    public function __construct(Container $container, Dispatcher $events, PermissionModuleManager $manager)
    {
        parent::__construct($container, $events);
        $this->manager = $manager;
    }

    /**
     * Name of event.
     *
     * @throws \Exception
     * @return string|object
     */
    protected function getEvent()
    {
        return PermissionModuleRegisterEvent::class;
    }

    /**
     * Handle Permission Register.
     */
    abstract public function handle();
}
