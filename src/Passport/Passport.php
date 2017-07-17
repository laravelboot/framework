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
namespace LaravelBoot\Foundation\Passport;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

/**
 * Class Passport.
 */
class Passport
{
    /**
     * @var \Illuminate\Container\Container|\LaravelBoot\Foundation\Application
     */
    protected $container;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * Passport constructor.
     *
     * @param \Illuminate\Container\Container|\LaravelBoot\Foundation\Application $container
     * @param \Illuminate\Events\Dispatcher                                  $events
     */
    public function __construct(Container $container, Dispatcher $events)
    {
        $this->container = $container;
        $this->events = $events;
    }

    /**
     * Call something.
     *
     * @return bool
     */
    public function call()
    {
        return true;
    }
}
