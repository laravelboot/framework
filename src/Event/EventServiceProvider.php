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
namespace LaravelBoot\Foundation\Event;

use Illuminate\Events\EventServiceProvider as IlluminateEventServiceProvider;

/**
 * Class EventServiceProvider.
 */
class EventServiceProvider extends IlluminateEventServiceProvider
{
    /**
     * Register for service provider.
     */
    public function register()
    {
        parent::register();
    }
}
