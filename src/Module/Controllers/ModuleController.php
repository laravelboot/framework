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
namespace LaravelBoot\Foundation\Module\Controllers;

use LaravelBoot\Foundation\Module\Handlers\EnableHandler;
use LaravelBoot\Foundation\Module\Handlers\InstallHandler;
use LaravelBoot\Foundation\Module\Handlers\ModuleHandler;
use LaravelBoot\Foundation\Module\Handlers\UninstallHandler;
use LaravelBoot\Foundation\Module\Handlers\UpdateHandler;
use LaravelBoot\Foundation\Routing\Abstracts\Controller;

/**
 * Class ModuleController.
 */
class ModuleController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::global::module::module.manage' => [
            'enable',
            'handle',
            'install',
            'uninstall',
            'update',
        ],
    ];

    /**
     * Enable handler.
     *
     * @param \LaravelBoot\Foundation\Module\Handlers\EnableHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function enable(EnableHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Handler.
     *
     * @param \LaravelBoot\Foundation\Module\Handlers\ModuleHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function handle(ModuleHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Install handler.
     *
     * @param \LaravelBoot\Foundation\Module\Handlers\InstallHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function install(InstallHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Uninstall handler.
     *
     * @param \LaravelBoot\Foundation\Module\Handlers\UninstallHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function uninstall(UninstallHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * Update Handler.
     *
     * @param \LaravelBoot\Foundation\Module\Handlers\UpdateHandler $handler
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function update(UpdateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
