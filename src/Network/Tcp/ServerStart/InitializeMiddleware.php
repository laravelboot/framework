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
namespace LaravelBoot\Foundation\Network\Tcp\ServerStart;

use LaravelBoot\Foundation\Network\Server\Middleware\MiddlewareInitiator;
use Illuminate\Support\Facades\Config;

class InitializeMiddleware
{
    private $laravelBootFilters = [];

    private $laravelBootTerminators = [];

    /**
     * @param $server
     */
    public function bootstrap($server)
    {
        $middlewarePath = Config::get('server.middleware.path');
        if (!is_dir($middlewarePath)) {
            return;
        }
        $middlewareInitiator = MiddlewareInitiator::getInstance();
        //$configs = ConfigLoader::getInstance()->load($middlewarePath);
        $middlewareConfig = isset($configs['middleware']) ? $configs['middleware'] : [];
        $middlewareConfig = is_array($middlewareConfig) ? $middlewareConfig : [];
        $middlewareInitiator->initConfig($middlewareConfig);
        $exceptionHandlerConfig = isset($configs['exceptionHandler']) ? $configs['exceptionHandler'] : [];
        $exceptionHandlerConfig = is_array($exceptionHandlerConfig) ? $exceptionHandlerConfig : [];
        $middlewareInitiator->initExceptionHandlerConfig($exceptionHandlerConfig);
        $middlewareInitiator->initFilters($this->laravelBootFilters);
        $middlewareInitiator->initTerminators($this->laravelBootTerminators);
    }
}
