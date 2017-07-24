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
namespace LaravelBoot\Foundation\Network\Http\ServerStart;

use LaravelBoot\Foundation\Network\Server\Middleware\MiddlewareInitiator;
use Illuminate\Support\Facades\Config;

class InitializeMiddleware
{
    private $zanFilters = [
        //\Zan\Framework\Network\Http\Middleware\SessionFilter::class,
    ];

    private $zanTerminators = [
        //\Zan\Framework\Network\Http\Middleware\SessionTerminator::class,
    ];

    /**
     * @param $server
     */
    public function bootstrap($server)
    {
        $middlewarePath = Config::get('server.paths.middleware');
        if (!is_dir($middlewarePath)) {
            return;
        }

        $middlewareInitiator = MiddlewareInitiator::getInstance();
        $middlewareConfig = ConfigLoader::getInstance()->load($middlewarePath);
        $exceptionHandlerConfig = isset($middlewareConfig['exceptionHandler']) ? $middlewareConfig['exceptionHandler'] : [];
        $exceptionHandlerConfig = is_array($exceptionHandlerConfig) ? $exceptionHandlerConfig : [];
        $middlewareConfig = isset($middlewareConfig['middleware']) ? $middlewareConfig['middleware'] : [];
        $middlewareConfig = is_array($middlewareConfig) ? $middlewareConfig : [];
        $middlewareInitiator->initConfig($middlewareConfig);
        $middlewareInitiator->initExceptionHandlerConfig($exceptionHandlerConfig);
        $middlewareInitiator->initZanFilters($this->zanFilters);
        $middlewareInitiator->initZanTerminators($this->zanTerminators);
    }
}
