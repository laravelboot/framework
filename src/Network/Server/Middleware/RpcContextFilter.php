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

use LaravelBoot\Foundation\Contracts\RequestFilter;
use LaravelBoot\Foundation\Network\Tcp\RpcContext;
use LaravelBoot\Foundation\Network\Tcp\Request as TcpRequest;
use LaravelBoot\Foundation\Network\Http\Request\Request as HttpRequest;


class RpcContextFilter implements RequestFilter
{
    public function doFilter(Request $request, Context $context)
    {
        /** @var RpcContext $rpcCtx */
        $rpcCtx = null;
        if ($request instanceof TcpRequest) {
            $rpcCtx = $request->getRpcContext();
        } else if ($request instanceof HttpRequest) {
            $rpcCtx = new RpcContext();
        }

        if ($rpcCtx) {
            $context->merge($rpcCtx->get(), false);
            $context->set("rpc-context", $rpcCtx);
        }
    }
}