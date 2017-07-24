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
namespace LaravelBoot\Foundation\Network\Http\Exception\Handler;

use LaravelBoot\Foundation\Contracts\ExceptionHandler;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Network\Http\Response\Response;
use LaravelBoot\Foundation\Network\Http\Response\JsonResponse;

class ServerUnavailableHandler implements ExceptionHandler
{
    public function handle(\Exception $e)
    {
        $code = $e->getCode();
        if ($code != 503) {
            yield false;
            return;
        }

        $errMsg = $e->getMessage();
        //$errorPagePath = '';
        //$errorPage = require $errorPagePath;
        $errorPage = '500 Error';//todo:错误显示


        $request = (yield getContext('request'));
        if ($request->wantsJson()) {
            $context = [
                'code' => $code,
                'msg' => $e->getMessage(),
                'data' => '',
            ];
            yield new JsonResponse($context);
        } else {
            //html
            yield new Response($errorPage, Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
