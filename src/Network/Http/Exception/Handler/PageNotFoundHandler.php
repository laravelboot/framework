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
use LaravelBoot\Foundation\Network\Http\Response\BaseResponse;
use LaravelBoot\Foundation\Network\Http\Response\RedirectResponse;
use LaravelBoot\Foundation\Network\Http\Response\Response;
use LaravelBoot\Foundation\Network\Http\Exception\PageNotFoundException;

class PageNotFoundHandler implements ExceptionHandler
{
    private $configKey = 'error';

    public function handle(\Exception $e)
    {
        if (!is_a($e, PageNotFoundException::class)) {
            return false;
        }
        $config = Config::get('server.'.$this->configKey, null);
        if (!$config) {
            return false;
        }
        // 跳转到配置的404页面
        return RedirectResponse::create($config['404'], BaseResponse::HTTP_FOUND);
    }
}
