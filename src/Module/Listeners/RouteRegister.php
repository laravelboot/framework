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
namespace LaravelBoot\Foundation\Module\Listeners;

use LaravelBoot\Foundation\Module\Controllers\ModuleController;
use LaravelBoot\Foundation\Routing\Abstracts\RouteRegister as AbstractRouteRegister;

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
        $this->router->group(['middleware' => ['auth:api', 'cross', 'web'], 'prefix' => 'api'], function () {
            $this->router->post('module/enable', ModuleController::class . '@enable');
            $this->router->post('module/install', ModuleController::class . '@install');
            $this->router->post('module/uninstall', ModuleController::class . '@uninstall');
            $this->router->post('module/update', ModuleController::class . '@update');
            $this->router->post('module', ModuleController::class . '@handle');
        });
    }
}
