<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/21 10:35
 * @version
 */
namespace LaravelBoot\Foundation\Network;

use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;
use LaravelBoot\Foundation\Network\Commands\ServerCommand;

class NetworkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            ServerCommand::class
        ]);
    }

    public function register()
    {
    }
}