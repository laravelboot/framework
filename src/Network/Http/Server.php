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
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeProxyIps;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeRouter;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeUrlRule;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeRouterSelfCheck;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeMiddleware;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeExceptionHandlerChain;
use LaravelBoot\Foundation\Network\Server\Monitor\Worker;
use LaravelBoot\Foundation\Network\Server\ServerStart\InitLogConfig;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeConnectionPool;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeErrorHandler;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeHawkMonitor;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeServiceChain;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeWorkerMonitor;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeServiceDiscovery;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeUrlConfig;
use LaravelBoot\Foundation\Network\Http\ServerStart\InitializeQiniuConfig;
use swoole_http_request as SwooleHttpRequest;
use swoole_http_response as SwooleHttpResponse;
use LaravelBoot\Foundation\Network\Server\ServerBase;
use LaravelBoot\Foundation\ServiceManager\ServerStore;
use LaravelBoot\Foundation\ServiceManager\ServiceDiscoveryInitiator;
use Illuminate\Support\Facades\Config;

class Server extends ServerBase
{
    protected $serverStartItems = [
        InitializeRouter::class,
        InitializeUrlRule::class,
        InitializeUrlConfig::class,
        InitializeRouterSelfCheck::class,
        InitializeMiddleware::class,
        InitializeExceptionHandlerChain::class,
        InitLogConfig::class,
        InitializeProxyIps::class,
    ];

    protected $workerStartItems = [
        InitializeErrorHandler::class,
        InitializeWorkerMonitor::class,
        InitializeHawkMonitor::class,
        InitializeConnectionPool::class,
        InitializeServiceDiscovery::class,
        InitializeServiceChain::class,
    ];

    public function setSwooleEvent()
    {
        $this->swooleServer->on('start', [$this, 'onStart']);
        $this->swooleServer->on('shutdown', [$this, 'onShutdown']);
        $this->swooleServer->on('managerStart', [$this, 'onManagerStart']);
        $this->swooleServer->on('workerStart', [$this, 'onWorkerStart']);
        $this->swooleServer->on('workerStop', [$this, 'onWorkerStop']);
        $this->swooleServer->on('workerError', [$this, 'onWorkerError']);

        $this->swooleServer->on('request', [$this, 'onRequest']);
    }

    protected function init()
    {
        $config = Config::get('registry');
        if (!isset($config['app_names']) || [] === $config['app_names']) {
            return;
        }
        //ServerStore::getInstance()->resetLockDiscovery();
        //register_shutdown_function([$this,'exceptionHandler']);
    }

    public function onStart($swooleServer)
    {
        $this->writePid($swooleServer->master_pid);
        swoole_set_process_name($this->getProcessName() . ':master');
        sys_echo("server starting .....[$swooleServer->host:$swooleServer->port]");
    }

    public function onManagerStart($swooleServer)
    {
        swoole_set_process_name($this->getProcessName() . ':manager');
    }

    public function onShutdown($swooleServer)
    {
        $this->removePidFile();
        sys_echo("server shutdown .....");
    }

    public function onWorkerStart($swooleServer, $workerId)
    {
        $_SERVER["WORKER_ID"] = $workerId;
        $this->bootWorkerStartItem($workerId);
        if($workerId >= $swooleServer->setting['worker_num']){
            swoole_set_process_name($this->getProcessName() . ':task:' . $workerId);
        }else{
            swoole_set_process_name($this->getProcessName() . ':worker:' . $workerId);
        }
        sys_echo("worker *$workerId starting .....");
    }

    public function onWorkerStop($swooleServer, $workerId)
    {
        ServiceDiscoveryInitiator::getInstance()->unlockDiscovery($workerId);
        sys_echo("worker *$workerId stopping .....");

        $num = Worker::getInstance()->reactionNum ?: 0;
        sys_echo("worker *$workerId still has $num requests in progress...");
    }

    public function onWorkerError($swooleServer, $workerId, $workerPid, $exitCode, $sigNo)
    {
        ServiceDiscoveryInitiator::getInstance()->unlockDiscovery($workerId);

        sys_echo("worker error happening [workerId=$workerId, workerPid=$workerPid, exitCode=$exitCode, signalNo=$sigNo]...");

        $num = Worker::getInstance()->reactionNum ?: 0;
        sys_echo("worker *$workerId still has $num requests in progress...");
    }

    public function onRequest(SwooleHttpRequest $swooleHttpRequest, SwooleHttpResponse $swooleHttpResponse)
    {
        (new RequestHandler())->handle($swooleHttpRequest, $swooleHttpResponse);
    }
}
