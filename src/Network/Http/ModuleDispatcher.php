<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/22 21:13
 * @version
 */
namespace LaravelBoot\Foundation\Network\Http;

use LaravelBoot\Foundation\Application;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Contracts\Request;
use LaravelBoot\Foundation\Contracts\Context;
use Illuminate\Http\Request as HttpRequest;
use LaravelBoot\Foundation\Network\Http\Response\Response;
use Illuminate\Support\Facades\Route;

class ModuleDispatcher
{
    public function dispatch(Request $request, Context $context)
    {
        $app = Application::getInstance();
        $router = $app['router'];
        $app->instance('LaravelBoot\Foundation\Contracts\Context',$context);
        $response = $router->dispatchToRoute($this->converIlluminateHttpRequest($request));
        if(is_a($response,Response::class)){
            yield $response;
        }else{
            yield new Response($response->getContent());
        }
    }

    protected function converIlluminateHttpRequest($request)
    {
        $httpRequest = HttpRequest::createFromBase($request);
        return $httpRequest;
    }
}