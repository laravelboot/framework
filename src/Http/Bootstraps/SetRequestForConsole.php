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
namespace LaravelBoot\Foundation\Http\Bootstraps;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Class SetRequestForConsole.
 */
class SetRequestForConsole
{
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        $url = $application->make('config')->get('app.url', 'http://localhost');
        $application->instance('request', Request::create($url, 'GET', [], [], [], $_SERVER));
    }
}
