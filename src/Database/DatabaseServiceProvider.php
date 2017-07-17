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
namespace LaravelBoot\Foundation\Database;

use Illuminate\Database\DatabaseServiceProvider as IlluminateDatabaseServiceProvider;

/**
 * Class DatabaseServiceProvider.
 */
class DatabaseServiceProvider extends IlluminateDatabaseServiceProvider
{
    /**
     * Boot service provider.
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }
}
