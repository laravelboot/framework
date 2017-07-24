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

use LaravelBoot\Foundation\Network\Http\RequestExceptionHandlerChain;

class InitializeExceptionHandlerChain
{
    /**
     * @param \LaravelBoot\Foundation\Network\Http\Server $server
     */
    public function bootstrap($server)
    {
        RequestExceptionHandlerChain::getInstance()->init();
    }
}
