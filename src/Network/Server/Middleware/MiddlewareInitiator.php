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
namespace LaravelBoot\Foundation\Network\Server\Middleware;

use LaravelBoot\Foundation\Utility\Singleton;

class MiddlewareInitiator
{
    use Singleton;

    public function initConfig(array $config = [])
    {
        $config['match'] = isset($config['match']) ? $config['match'] : [];
        MiddlewareConfig::getInstance()->setConfig($config);
    }

    public function initExceptionHandlerConfig(array $exceptionHandlerConfig)
    {
        $exceptionHandlerConfig['match'] = isset($exceptionHandlerConfig['match']) ? $exceptionHandlerConfig['match'] : [];
        MiddlewareConfig::getInstance()->setExceptionHandlerConfig($exceptionHandlerConfig);
    }

    public function initFilters(array $filters = [])
    {
        MiddlewareConfig::getInstance()->setFilters($filters);
    }

    public function initTerminators(array $terminators = [])
    {
        MiddlewareConfig::getInstance()->setTerminators($terminators);
    }
} 