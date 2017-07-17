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
namespace LaravelBoot\Foundation\Setting\Controllers;

use LaravelBoot\Foundation\Routing\Abstracts\Controller;
use LaravelBoot\Foundation\Setting\Handlers\AllHandler;
use LaravelBoot\Foundation\Setting\Handlers\GetHandler;
use LaravelBoot\Foundation\Setting\Handlers\SetHandler;

/**
 * Class SettingController.
 */
class SettingController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::global::global::setting.get' => 'get',
        'global::global::global::setting.set' => 'set',
    ];

    /**
     * All handler.
     *
     * @param \LaravelBoot\Foundation\Setting\Handlers\AllHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse
     * @throws \Exception
     */
    public function all(AllHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Get handler.
     *
     * @param GetHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function get(GetHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Set handler.
     *
     * @param \LaravelBoot\Foundation\Setting\Handlers\SetHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse
     * @throws \Exception
     */
    public function set(SetHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
