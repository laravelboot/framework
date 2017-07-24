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
namespace LaravelBoot\Foundation\Network\Http;

use LaravelBoot\Foundation\Exception\ExceptionHandlerChain;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\BizErrorHandler;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\ForbiddenHandler;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\InternalErrorHandler;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\InvalidRouteHandler;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\PageNotFoundHandler;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\RedirectHandler;
use LaravelBoot\Foundation\Network\Http\Exception\Handler\ServerUnavailableHandler;
use LaravelBoot\Foundation\Utility\Singleton;

class RequestExceptionHandlerChain extends ExceptionHandlerChain
{
    use Singleton;

    private $handles = [
        RedirectHandler::class,
        PageNotFoundHandler::class,
        ForbiddenHandler::class,
        InvalidRouteHandler::class,
        BizErrorHandler::class,
        ServerUnavailableHandler::class,
        InternalErrorHandler::class,
    ];

    public function init()
    {
        $this->addHandlersByName($this->handles);
    }
}
