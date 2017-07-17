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
use LaravelBoot\Foundation\Permission\Events\PermissionTypeRegister as PermissionTypeRegisterEvent;
use LaravelBoot\Foundation\Permission\PermissionTypeManager;

/**
 * Class PermissionTypeRegister.
 */
abstract class PermissionTypeRegister extends EventSubscriber
{
    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionTypeManager
     */
    protected $manager;

    /**
     * PermissionTypeRegister constructor.
     *
     * @param \Illuminate\Container\Container                     $container
     * @param \Illuminate\Events\Dispatcher                       $events
     * @param \LaravelBoot\Foundation\Permission\PermissionTypeManager $manager
     */
    public function __construct(Container $container, Dispatcher $events, PermissionTypeManager $manager)
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
        return PermissionTypeRegisterEvent::class;
    }

    /**
     * Handle Permission Register.
     */
    abstract public function handle();
}
