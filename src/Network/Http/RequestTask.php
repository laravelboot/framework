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
namespace LaravelBoot\Foundation\Network\Http;

use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Network\Http\Response\JsonResponse;
use swoole_http_response as SwooleHttpResponse;
use LaravelBoot\Foundation\Contracts\Request;
use LaravelBoot\Foundation\Coroutine\Task;
use LaravelBoot\Foundation\Network\Http\Response\BaseResponse;
use LaravelBoot\Foundation\Network\Http\Response\InternalErrorResponse;
use LaravelBoot\Foundation\Network\Http\Response\ResponseTrait;
use LaravelBoot\Foundation\Network\Server\Middleware\MiddlewareManager;
use LaravelBoot\Foundation\Contracts\Context;

class RequestTask
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var SwooleHttpResponse
     */
    private $swooleResponse;
    /**
     * @var Context
     */
    private $context;

    private $middleWareManager;

    public function __construct(Request $request, SwooleHttpResponse $swooleResponse, Context $context, MiddlewareManager $middlewareManager)
    {
        $this->request = $request;
        $this->swooleResponse = $swooleResponse;
        $this->context = $context;
        $this->middleWareManager = $middlewareManager;
    }

    public function run()
    {
        try {
            yield $this->doRun();
            return;
        }catch (\Exception $e) {
        } finally {
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
        }
        $coroutine = $this->middleWareManager->handleHttpException($e);
        Task::execute($coroutine, $this->context);
        $this->context->getEvent()->fire($this->context->get('request_end_event_name'));
    }

    public function doRun()
    {
        $response = (yield $this->middleWareManager->executeFilters());
        if (null !== $response) {
            $this->context->set('response', $response);
            /** @var ResponseTrait $response */
            yield $response->sendBy($this->swooleResponse);
            $this->context->getEvent()->fire($this->context->get('request_end_event_name'));
            return;
        }

        $dispatcher = Application::getInstance()->make(ModuleDispatcher::class);
        $response = (yield $dispatcher->dispatch($this->request, $this->context));
        if (null === $response) {
            $code = BaseResponse::HTTP_INTERNAL_SERVER_ERROR;
            $response = new InternalErrorResponse("network error ($code)", $code);
            //$response->withHeader('Content-Type','text/html; charset=utf-8');
        }

        yield $this->middleWareManager->executePostFilters($response);

        $this->context->set('response', $response);
        yield $response->sendBy($this->swooleResponse);

        $this->context->getEvent()->fire($this->context->get('request_end_event_name'));
    }
}
