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
use LaravelBoot\Foundation\Network\Http\Response\BaseResponse;
use LaravelBoot\Foundation\Network\Http\Response\RedirectResponse;
use LaravelBoot\Foundation\Network\Http\Exception\RedirectException;

class RedirectHandler implements ExceptionHandler
{
    public function handle(\Exception $e)
    {
        if (!isset($e->redirectUrl) && !is_a($e, RedirectException::class)) {
            return null;
        }

        return RedirectResponse::create($e->redirectUrl, BaseResponse::HTTP_FOUND);
    }
}
