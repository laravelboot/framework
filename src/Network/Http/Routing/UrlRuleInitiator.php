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
namespace LaravelBoot\Foundation\Network\Http\Routing;

use LaravelBoot\Foundation\Utility\Singleton;

class UrlRuleInitiator
{
    use Singleton;

    public function init()
    {
        UrlRule::getInstance()->loadRules();
    }
} 