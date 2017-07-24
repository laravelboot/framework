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

use LaravelBoot\Foundation\Contracts\Request;
use LaravelBoot\Foundation\Contracts\RequestTerminator;
use LaravelBoot\Foundation\Contracts\Context;

class ClearContextTerminator implements RequestTerminator
{
    public function terminate(Request $request,$response, Context $context)
    {
        $context = (yield getContextObject());
        if ($context instanceof Context) {
            $context->clear();
        }
        unset($context);
        $context = null;
    }
}

