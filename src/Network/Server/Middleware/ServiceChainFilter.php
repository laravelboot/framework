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
use LaravelBoot\Foundation\Contracts\RequestFilter;
use LaravelBoot\Foundation\Contracts\Context;

use LaravelBoot\Foundation\Network\Tcp\RpcContext;
use Illuminate\Container\Container;
use LaravelBoot\Foundation\Network\Contracts\ServiceChainer;
use LaravelBoot\Foundation\Network\Tcp\Request as TcpRequest;
use LaravelBoot\Foundation\Network\Http\Request\Request as HttpRequest;

class ServiceChainFilter implements RequestFilter
{
    public function doFilter(Request $request, Context $context)
    {
        $container = Container::getInstance();

        if (isset($container[ServiceChainer::class])) {

            $chainValue = null;

            /** @var ServiceChainer $serviceChain */
            $serviceChain = $container->make(ServiceChainer::class);

            /** @var RpcContext $rpcCtx */
            $rpcCtx = $context->get("rpc-context");

            if ($request instanceof TcpRequest) {
                $chainValue = $serviceChain->getChainValue(ServiceChainer::TYPE_TCP, $rpcCtx->get());
            } else if ($request instanceof HttpRequest) {
                $swooleRequest = $context->get("swoole_request");
                $chainValue = $serviceChain->getChainValue(ServiceChainer::TYPE_HTTP, $swooleRequest->header);
            }

            if ($chainValue === null && getenv("ZAN_JOB_MODE")) {
                $chainValue = $serviceChain->getChainValue(ServiceChainer::TYPE_JOB);
            }

            if ($chainValue !== null) {
                $jsonValue = json_encode(["name" => $chainValue]);

                $novaKey = $serviceChain->getChainKey(ServiceChainer::TYPE_TCP);
                $httpKey = $serviceChain->getChainKey(ServiceChainer::TYPE_HTTP);

                $rpcCtx->set($novaKey, $jsonValue);
                $rpcCtx->set($httpKey, $jsonValue);

                $context->set("service-chain", $serviceChain);
                $context->set("service-chain-value", $chainValue);
            }
        }
    }
}