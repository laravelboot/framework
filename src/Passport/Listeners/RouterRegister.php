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
namespace LaravelBoot\Foundation\Passport\Listeners;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\ApiTokenCookieFactory;
use LaravelBoot\Foundation\Passport\Controllers\AccessTokenController;
use LaravelBoot\Foundation\Passport\Controllers\AuthorizationController;
use LaravelBoot\Foundation\Passport\Controllers\ClientsController;
use LaravelBoot\Foundation\Routing\Abstracts\RouteRegister;

/**
 * Class RouterRegister.
 */
class RouterRegister extends RouteRegister
{
    /**
     * Handle Route Register.
     */
    public function handle()
    {
        $this->router->group(['prefix' => 'oauth'], function () {
            $this->router->post('access', AccessTokenController::class . '@issueToken');
        });
        $this->router->group(['middleware' => ['web', 'auth'], 'prefix' => 'oauth'], function () {
            $this->router->delete('access/authorize', AuthorizationController::class . '@deny');
            $this->router->resource('authorize', AuthorizationController::class);
            $this->router->resource('clients', ClientsController::class);
            $this->router->post('refresh', function (ApiTokenCookieFactory $cookieFactory, Request $request) {
                return (new Response('Refreshed.'))->withCookie($cookieFactory->make($request->user()->getKey(),
                    $request->session()->token()));
            });
            $this->router->resource('access/token', AccessTokenController::class, [
                'only' => ['index', 'store'],
            ]);
        });
    }
}
