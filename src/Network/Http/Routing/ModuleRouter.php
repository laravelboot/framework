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

use LaravelBoot\Foundation\Network\Http\Request\Request;

class ModuleRouter implements IRouter
{
    public function dispatch(Request $request)
    {
        $separator = "/";
        $parts = array_filter(explode($separator, trim($request->getRoute(), $separator)));
        $actionName = array_pop($parts);
        $controllerName = array_pop($parts);
        $moduleName = join($separator, $parts);
        return [$moduleName, $controllerName, $actionName];
    }
}