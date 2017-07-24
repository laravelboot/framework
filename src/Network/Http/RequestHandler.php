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

use Illuminate\Support\Facades\Log;
use LaravelBoot\Foundation\Application;
use swoole_http_request as SwooleHttpRequest;
use swoole_http_response as SwooleHttpResponse;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Coroutine\Signal;
use LaravelBoot\Foundation\Coroutine\Task;
use LaravelBoot\Foundation\Network\Exception\ExcessConcurrencyException;
use LaravelBoot\Foundation\Network\Http\Request\Request;
use LaravelBoot\Foundation\Network\Http\Response\BaseResponse;
use LaravelBoot\Foundation\Network\Http\Response\InternalErrorResponse;
use LaravelBoot\Foundation\Network\Http\Response\JsonResponse;
use LaravelBoot\Foundation\Network\Http\Routing\Router;
use LaravelBoot\Foundation\Network\Server\Middleware\MiddlewareManager;
use LaravelBoot\Foundation\Network\Server\Monitor\Worker;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Contracts\Context;
use LaravelBoot\Foundation\Utility\Types\Time;

use LaravelBoot\Foundation\Http\Kernel;

class RequestHandler
{
    private $context = null;

    /** @var MiddlewareManager */
    private $middleWareManager = null;

    /** @var Task */
    private $task = null;
    private $event = null;

    /** @var Request */
    private $request = null;

    const DEFAULT_TIMEOUT = 30 * 1000;

    public function __construct()
    {
        $this->context = new Context();
        $this->event = $this->context->getEvent();
    }

    public function handle(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse)
    {
        try {
            $request = Request::createFromSwooleHttpRequest($swooleRequest);
            if (false === $this->initContext($request, $swooleRequest, $swooleResponse)) {
                //filter ico file access
                return;
            }

            $this->middleWareManager = new MiddlewareManager($request, $this->context);
            $isAccept = Worker::getInstance()->reactionReceive();
            //限流
            if (!$isAccept) {
                throw new ExcessConcurrencyException('现在访问的人太多,请稍后再试..', 503);
            }

            //bind event
            $timeout = $this->context->get('request_timeout');
            $this->event->once($this->getRequestFinishJobId(), [$this, 'handleRequestFinish']);
            Timer::after($timeout, [$this, 'handleTimeout'], $this->getRequestTimeoutJobId());

            $requestTask = new RequestTask($request, $swooleResponse, $this->context, $this->middleWareManager);
            $coroutine = $requestTask->run();
            $this->task = new Task($coroutine, $this->context);
            $this->task->run();
            return;
        }catch (\Exception $e) {
        }finally{
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
        }

        if($debug=true){
            echo_exception($e);
        }

        if ($this->middleWareManager) {
            $coroutine = $this->middleWareManager->handleHttpException($e);
        } else {
            $coroutine = RequestExceptionHandlerChain::getInstance()->handle($e);
        }
        Task::execute($coroutine, $this->context);
        $this->event->fire($this->getRequestFinishJobId());
    }

    private function initContext(Request $request, SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse)
    {
        $this->request = $request;
        $this->context->set('swoole_request', $swooleRequest);
        $this->context->set('request', $request);
        $this->context->set('swoole_response', $swooleResponse);

        $cookie = new Cookie($request, $swooleResponse);
        $this->context->set('cookie', $cookie);

        $this->context->set('request_time', Time::stamp());
        $request_timeout = Config::get('server.request_timeout');
        $request_timeout = $request_timeout ? $request_timeout : self::DEFAULT_TIMEOUT;
        $this->context->set('request_timeout', $request_timeout);

        $this->context->set('request_end_event_name', $this->getRequestFinishJobId());
    }

    public function handleRequestFinish()
    {
        Timer::clearAfterJob($this->getRequestTimeoutJobId());
        $response = $this->context->get('response');
        $coroutine = $this->middleWareManager->executeTerminators($response);
        Task::execute($coroutine, $this->context);
    }

    public function handleTimeout()
    {
        try {
            if($this->task){
                $this->task->setStatus(Signal::TASK_KILLED);
            }
            $this->logTimeout();

            $request = $this->context->get('request');
            if ($request && $request->wantsJson()) {
                $data = [
                    'code' => 10000,
                    'msg' => '网络超时',
                    'data' => '',
                ];
                $response = new JsonResponse($data, BaseResponse::HTTP_GATEWAY_TIMEOUT);
            } else {
                $response = new InternalErrorResponse('服务器超时', BaseResponse::HTTP_GATEWAY_TIMEOUT);
            }

            $this->context->set('response', $response);
            $swooleResponse = $this->context->get('swoole_response');
            $response->sendBy($swooleResponse);
            $this->event->fire($this->getRequestFinishJobId());
        }catch (\Exception $ex) {
            echo_exception($ex);
        }
    }

    private function logTimeout()
    {
        // 注意: 此处需要配置 server.proxy
        $remoteIp = $this->request->getClientIp();
        $route = $this->request->getRoute();
        $query = http_build_query($this->request->query->all());
        sys_error("SERVER TIMEOUT [remoteIP=$remoteIp, url=$route?$query]");
    }

    private function getRequestFinishJobId()
    {
        return spl_object_hash($this) . '_request_finish';
    }

    private function getRequestTimeoutJobId()
    {
        return spl_object_hash($this) . '_handle_timeout';
    }
}
