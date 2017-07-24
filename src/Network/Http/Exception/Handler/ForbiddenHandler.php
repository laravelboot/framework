<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/21 23:40
 * @version
 */
namespace LaravelBoot\Foundation\Network\Http\Exception\Handler;

use Exception;
use LaravelBoot\Foundation\Contracts\ExceptionHandler;
use LaravelBoot\Foundation\Network\Http\Response\JsonResponse;
use LaravelBoot\Foundation\Network\Http\Response\Response;
use LaravelBoot\Foundation\Network\Http\Exception\TokenException;

class ForbiddenHandler implements ExceptionHandler
{
    public function handle(Exception $e)
    {
        if ($e instanceof TokenException) {
            $errMsg = '禁止访问';
            $errorPage = 'access is not allowed';

            $request = (yield getContext('request'));
            if ($request->wantsJson()) {
                $context = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                    'data' => '',
                ];
                yield new JsonResponse($context);
            } else {
                //html
                yield new Response($errorPage, Response::HTTP_FORBIDDEN);
            }
        }
    }
}