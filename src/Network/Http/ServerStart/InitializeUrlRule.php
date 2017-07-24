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

use LaravelBoot\Foundation\Network\Http\Routing\UrlRuleInitiator;
use LaravelBoot\Foundation\Application;

class InitializeUrlRule
{
    /**
     * @param $server
     */
    public function bootstrap($server)
    {
        UrlRuleInitiator::getInstance()->init();
    }
} 