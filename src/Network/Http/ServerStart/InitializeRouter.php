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

use LaravelBoot\Foundation\Application;
use Illuminate\Support\Facades\Config;

class InitializeRouter
{
    /**
     * @param $server
     */
    public function bootstrap($server)
    {
        $app = Application::getInstance();
        $router = $app['router'];
        $router->get('/admin/home/index',function(){
            return ['code'=>200,'data'=>['title'=>'Hello World']];
        });

    }
}