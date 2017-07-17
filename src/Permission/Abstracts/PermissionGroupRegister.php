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
use LaravelBoot\Foundation\Permission\Events\PermissionRegister as PermissionRegisterEvent;
use LaravelBoot\Foundation\Permission\PermissionGroupManager;

/**
 * Class PermissionGroupRegister.
 */
abstract class PermissionGroupRegister extends EventSubscriber
{
    /**
     * @var \LaravelBoot\Foundation\Permission\PermissionManager
     */
    protected $manager;

    /**
     * PermissionRegister constructor.
     *
     * @param \Illuminate\Container\Container                      $container
     * @param \Illuminate\Events\Dispatcher                        $events
     * @param \LaravelBoot\Foundation\Permission\PermissionGroupManager $manager
     */
    public function __construct(Container $container, Dispatcher $events, PermissionGroupManager $manager)
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
        return PermissionRegisterEvent::class;
    }

    /**
     * Handle Permission Group Register.
     */
    abstract public function handle();
}
