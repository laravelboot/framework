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
namespace LaravelBoot\Foundation\Network\Server;

use RuntimeException;
use LaravelBoot\Foundation\Application;
use Illuminate\Support\Facades\Config;
use swoole_http_server as SwooleHttpServer;
use swoole_server as SwooleTcpServer;
use swoole_websocket_server as SwooleWebSocketServer;
use LaravelBoot\Foundation\Network\Http\Server as HttpServer;
use LaravelBoot\Foundation\Network\Tcp\Server as TcpServer;
use LaravelBoot\Foundation\Network\WebSocket\Server as WebSocketServer;

class Factory
{
    private $configName;
    private $host;
    private $port;
    private $serverConfig;

    public function __construct($configName)
    {
        $this->configName = $configName;
    }

    private function validConfig($host,$port,$daemon,$config)
    {
        $config = Config::get($this->configName);
        if (empty($config)) {
            throw new RuntimeException('server config not found');
        }

        $this->host = $host ? : $config['host'];
        $this->port = $port ? : $config['port'];
        $this->serverConfig = $config['config'];
        if(is_bool($daemon)){
            $this->serverConfig['daemonize'] = $daemon ? 1 : 0;
        }
        if (empty($this->host) || empty($this->port)) {
            throw new RuntimeException('server config error: empty ip/port');
        }

        $this->serverConfig["max_request"] = 0;
    }

    /**
     * @param $host
     * @param $port
     * @param $daemon
     * @param $config
     * @return \LaravelBoot\Foundation\Network\Http\Server
     */
    public function createHttpServer($host,$port,$daemon,$config)
    {
        $this->validConfig($host,$port,$daemon,$config);
        $params = ['host'=>$this->host, 'port'=>$this->port,'mode'=>SWOOLE_PROCESS,'sock_type'=>SWOOLE_SOCK_TCP];
        $swooleServer = Application::getInstance()->make(SwooleHttpServer::class,$params);

        $server =Application::getInstance()->make(HttpServer::class, ['swooleServer'=>$swooleServer, 'config'=>$this->serverConfig]);

        return $server;
    }

    /**
     * @param $host
     * @param $port
     * @param $daemon
     * @param $config
     * @return \LaravelBoot\Foundation\Network\Tcp\Server
     */
    public function createTcpServer($host,$port,$daemon,$config)
    {
        $this->validConfig($host,$port,$daemon,$config);
        $params = ['host'=>$this->host, 'port'=>$this->port,'mode'=>SWOOLE_PROCESS,'sock_type'=>SWOOLE_SOCK_TCP];
        $swooleServer = Application::getInstance()->make(SwooleTcpServer::class,$params);
        $server = Application::getInstance()->make(TcpServer::class, ['swooleServer'=>$swooleServer, 'config'=>$this->serverConfig]);

        return $server;
    }

    /**
     * @param $host
     * @param $port
     * @param $daemon
     * @param $config
     * @return \LaravelBoot\Foundation\Network\Http\WebSocketServer
     */
    public function createWebSocketServer($host,$port,$daemon,$config)
    {
        $this->validConfig($host,$port,$daemon,$config);

        if (isset($this->serverConfig['dispatch_mode'])) {
            if ($this->serverConfig['dispatch_mode'] == 1 || $this->serverConfig['dispatch_mode'] == 3) {
                sys_error("dispatch_mode can not be set 1 or 3 in websocket server, change it to default(2)");
                unset($this->serverConfig['dispatch_mode']);
            }
        }
        $swooleServer = Application::getInstance()->make(SwooleWebSocketServer::class, [$this->host, $this->port]);

        $server = Application::getInstance()->make(WebSocketServer::class, [$swooleServer, $this->serverConfig]);

        return $server;
    }
}