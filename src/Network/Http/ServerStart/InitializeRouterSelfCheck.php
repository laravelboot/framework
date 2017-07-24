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

use LaravelBoot\Foundation\Network\Http\Routing\RouterSelfCheckInitiator;
use LaravelBoot\Foundation\Application;

class InitializeRouterSelfCheck
{
    /**
     * @param $server
     */
    public function bootstrap($server)
    {
        RouterSelfCheckInitiator::getInstance()->init();
    }
} 