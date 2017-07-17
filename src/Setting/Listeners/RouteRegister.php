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
namespace LaravelBoot\Foundation\Setting\Listeners;

use LaravelBoot\Foundation\Routing\Abstracts\RouteRegister as AbstractRouteRegister;
use LaravelBoot\Foundation\Setting\Controllers\SettingController;

/**
 * Class RouteRegister.
 */
class RouteRegister extends AbstractRouteRegister
{
    /**
     * Handle Route Register.
     */
    public function handle()
    {
        $this->router->group(['middleware' => ['auth:api', 'cross', 'web'], 'prefix' => 'api/setting'], function () {
            $this->router->post('all', SettingController::class . '@all');
            $this->router->post('get', SettingController::class . '@get');
            $this->router->post('set', SettingController::class . '@set');
        });
    }
}
