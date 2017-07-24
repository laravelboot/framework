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
namespace LaravelBoot\Foundation\Routing\Abstracts;

use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Routing\Controller as IlluminateController;
use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Routing\Traits\Flowable;
use LaravelBoot\Foundation\Routing\Traits\Logable;
use LaravelBoot\Foundation\Routing\Traits\Permissionable;
use LaravelBoot\Foundation\Routing\Traits\Settingable;
use LaravelBoot\Foundation\Routing\Traits\Viewable;
use LaravelBoot\Foundation\Validation\ValidatesRequests;

/**
 * Class Controller.
 */
abstract class Controller extends IlluminateController
{
    use Flowable, Logable, Permissionable, Settingable, ValidatesRequests, Viewable;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Illuminate\Routing\Redirector
     */
    protected $redirector;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \LaravelBoot\Foundation\Contracts\Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->container = $this->getContainer();
        $this->events = $this->container->make('events');
        $this->redirector = $this->container->make('redirect');
        $this->request = $this->container->make('request');
        $this->context = $this->container->make('context');
        if ($this->permissions) {
            foreach ($this->permissions as $permission=>$methods) {
                $this->middleware('permission:' . $permission)->only($methods);
            }
        }
    }

    /**
     * Get a command from console instance.
     *
     * @param string $name
     *
     * @return \LaravelBoot\Foundation\Console\Abstracts\Command|\Symfony\Component\Console\Command\Command
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getCommand($name)
    {
        return $this->getConsole()->get($name);
    }

    /**
     * Get configuration instance.
     *
     * @return \LaravelBoot\Foundation\Configuration\Repository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getConfig()
    {
        return $this->container->make('config');
    }

    /**
     * Get console instance.
     *
     * @return \Illuminate\Contracts\Console\Kernel|\LaravelBoot\Foundation\Console\Application
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getConsole()
    {
        $kernel = $this->container->make(Kernel::class);
        $kernel->bootstrap();

        return $kernel->getArtisan();
    }

    /**
     * Get IoC Container.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return Application::getInstance();
    }

    /**
     * Get mailer instance.
     *
     * @return \Illuminate\Mail\Mailer
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getMailer()
    {
        return $this->container->make('mailer');
    }

    /**
     * Get session instance.
     *
     * @return \Illuminate\Session\Store
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getSession()
    {
        return $this->container->make('session');
    }
}
