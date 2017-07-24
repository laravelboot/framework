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

use Illuminate\Support\Facades\Log;
use LaravelBoot\Foundation\Contracts\ExceptionHandler;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Network\Http\Response\BaseResponse;
use LaravelBoot\Foundation\Network\Http\Response\RedirectResponse;
use LaravelBoot\Foundation\Network\Http\Response\Response;

class InternalErrorHandler implements ExceptionHandler
{
    private $configKey = 'error';

    public function handle(\Exception $e)
    {
        if (!is_a($e, \Exception::class)) {
            return false;
        }

        try {
            $config = Config::get('server.'.$this->configKey, null);
            if (!$config) {
                $code = $e->getCode();
                Log::error($e->getMessage() . PHP_EOL .$e->getTraceAsString());
                return new Response("Internal Error ($code):" . $e);
            }
            // 跳转到配置的500页面
            if (isset($config['500'])) {
                return RedirectResponse::create($config['500'], BaseResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            $errMsg = '对不起，页面被霸王龙吃掉了... ';
            $errorPage = 'Server Internal Error';
            return new Response($errorPage);
        }catch (\Exception $e) {
            return $this->handle($e);
        }
    }
}
