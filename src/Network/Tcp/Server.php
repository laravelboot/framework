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
namespace LaravelBoot\Foundation\Network\Tcp;

use LaravelBoot\Foundation\Network\Server\Monitor\Worker;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeErrorHandler;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeEtcdTTLRefreshing;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeHawkMonitor;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeServiceChain;
use LaravelBoot\Foundation\ServiceManager\ServiceDiscoveryInitiator;
use LaravelBoot\Foundation\ServiceManager\ServerStore;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeServiceDiscovery;
use LaravelBoot\Foundation\Network\Server\ServerStart\InitLogConfig;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeConnectionPool;
use swoole_server as SwooleServer;
use LaravelBoot\Foundation\Application;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Exception\LaravelBootException;
use LaravelBoot\Foundation\Network\Server\ServerBase;
use LaravelBoot\Foundation\Network\Tcp\ServerStart\InitializeMiddleware;
use LaravelBoot\Foundation\Network\Tcp\ServerStart\InitializeSqlMap;
use LaravelBoot\Foundation\Network\Server\WorkerStart\InitializeWorkerMonitor;
use LaravelBoot\Foundation\Network\Tcp\WorkerStart\InitializeServiceRegister;
use LaravelBoot\Foundation\ServiceManager\ServiceUnregister;

class Server extends ServerBase
{

    protected $serverStartItems = [
        InitLogConfig::class,
        InitializeMiddleware::class
    ];

    protected $workerStartItems = [
        InitializeErrorHandler::class,
        InitializeWorkerMonitor::class,
        InitializeConnectionPool::class,
        InitializeServiceDiscovery::class,
        InitializeServiceChain::class,
        InitializeHawkMonitor::class,
    ];

    public function setSwooleEvent()
    {
        $this->swooleServer->on('start', [$this, 'onStart']);
        $this->swooleServer->on('shutdown', [$this, 'onShutdown']);
        $this->swooleServer->on('managerStart', [$this, 'onManagerStart']);
        $this->swooleServer->on('workerStart', [$this, 'onWorkerStart']);
        $this->swooleServer->on('workerStop', [$this, 'onWorkerStop']);
        $this->swooleServer->on('workerError', [$this, 'onWorkerError']);

        $this->swooleServer->on('connect', [$this, 'onConnect']);
        $this->swooleServer->on('receive', [$this, 'onReceive']);
        $this->swooleServer->on('close', [$this, 'onClose']);
    }

    protected function init()
    {
        $config = Config::get('registry.novaApi', null);
        if(null === $config){
            return true;
        }

        //Nova::init($this->parserNovaConfig($config));

        $config = Config::get('registry');
        if (isset($config['app_names']) && is_array($config['app_names']) && [] !== $config['app_names']) {
            ServerStore::getInstance()->resetLockDiscovery();
        }
    }

    public function onConnect()
    {
        sys_echo("connecting ......");
    }

    public function onClose()
    {
        sys_echo("closing .....");
    }

    public function onManagerStart($swooleServer)
    {
        swoole_set_process_name($this->getProcessName() . ':manager');
    }

    public function onStart($swooleServer)
    {
        $this->writePid($swooleServer->master_pid);
        Application::getInstance()->make(InitializeServiceRegister::class)->bootstrap($this);
        sys_echo("server starting ..... [$swooleServer->host:$swooleServer->port]");
    }

    public function onShutdown($swooleServer)
    {
        $this->removePidFile();
        (new ServiceUnregister())->unRegister();
        sys_echo("server shutdown .....");
    }

    public function onWorkerStart($swooleServer, $workerId)
    {
        $_SERVER["WORKER_ID"] = intval($workerId);
        $this->bootWorkerStartItem($workerId);
        sys_echo("worker *$workerId starting .....");
    }

    public function onWorkerStop($swooleServer, $workerId)
    {
        ServiceDiscoveryInitiator::getInstance()->unlockDiscovery($workerId);
        sys_echo("worker *$workerId stopping ....");

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

    public function onPacket(SwooleServer $swooleServer, $data, array $clientInfo)
    {
        sys_echo("receive packet data..");
    }

    public function onReceive(SwooleServer $swooleServer, $fd, $fromId, $data)
    {
        (new RequestHandler())->handle($swooleServer, $fd, $fromId, $data);
    }

    /**
     * 配置向下兼容
     *
     * novaApi => [
     *      'path'  => 'vendor/nova-service/xxx/gen-php',
     *      'namespace' => 'Com\\Youzan\\Biz\\',
     *      'appName' => 'demo', // optional
     *      'domain' => 'com.youzan.service', // optional
     * ]
     * novaApi => [
     *      [
     *          'appName' => 'app-foo',
     *          'path'  => 'vendor/nova-service/xxx/gen-php',
     *          'namespace' => 'Com\\Youzan\\Biz\\',
     *          'domain' => 'com.youzan.service', // optional
     *      ],
     *      [
     *          'appName' => 'app-bar',
     *          'path'  => 'vendor/nova-service/xxx/gen-php',
     *          'namespace' => 'Com\\Youzan\\Biz\\',
     *          'domain' => 'com.youzan.service', // optional
     *      ],
     * ]
     * @param $config
     * @return array
     * @throws LaravelBootException
     */
    private function parserNovaConfig($config)
    {
        if (!is_array($config)) {
            throw new LaravelBootException("invalid nova config[novaApi]");
        }
        if (isset($config["path"])) {
            $appName = Application::getInstance()->getName();
            if (!isset($config["appName"])) {
                $config["appName"] = $appName;
            }
            $config = [ $config ];
        }

        foreach ($config as &$item) {
            if (!isset($item["appName"])) {
                $item["appName"] = Application::getInstance()->getName();
            }
            if(!isset($item["path"])){
                throw new LaravelBootException("nova server path not defined[novaApi.path]");
            }

            $item["path"] = Path::getRootPath() . $item["path"];

            if(!isset($item["namespace"])){
                throw new LaravelBootException("nova namespace path not defined[novaApi.namespace]");
            }

            if(!isset($item["domain"])) {
                $item["domain"] = "com.youzan.service";
            }

            if(!isset($item["protocol"])) {
                $item["protocol"] = "nova";
            }
        }
        unset($item);
        return $config;
    }
}
