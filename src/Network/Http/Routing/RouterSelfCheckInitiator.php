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
use LaravelBoot\Foundation\Utility\Types\Arr;
use LaravelBoot\Foundation\Utility\Types\Dir;
use Illuminate\Support\Facades\Config;

class RouterSelfCheckInitiator
{
    use Singleton;

    public function init()
    {
        $routerSelfCheck = RouterSelfCheck::getInstance();
        $routerSelfCheck->setUrlRules(UrlRule::getRules());
        $routerSelfCheck->setCheckList($this->_getCheckList());
        $routerSelfCheck->check();
    }

    private function _getCheckList()
    {
        $checkList = [];
        $checkListFiles = Dir::glob(Config::get('server.paths.routing'), '*.check.php');
        if (!is_array($checkListFiles) or empty($checkListFiles) ) {
            return [];
        }
        foreach ($checkListFiles as $file)
        {
            $list = include $file;
            if (!is_array($list)) continue;
            $checkList = Arr::merge($checkList, $list);
        }
        return $checkList;
    }
} 