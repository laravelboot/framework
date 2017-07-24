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

use LaravelBoot\Foundation\Utility\Types\Arr;
use LaravelBoot\Foundation\Utility\Types\Dir;
use LaravelBoot\Foundation\Utility\Singleton;
use Illuminate\Support\Facades\Config;

class UrlRule
{
    use Singleton;

    private static $rules = [];

    public static function loadRules()
    {
        $routeFiles = Dir::glob(Config::get('server.paths.routing'),'*.routing.php');

        if(!$routeFiles) return false;

        foreach($routeFiles as $file){
            $route = include $file;
            if(!is_array($route)) continue;
            self::$rules = Arr::merge(self::$rules,$route);
        }
    }

    public static function getRules()
    {
        return self::$rules;
    }
}