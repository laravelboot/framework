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

interface IRouter
{
    /*
     * @return array ['module', 'controller', 'action']
     */
    public function dispatch(Request $request);
}
