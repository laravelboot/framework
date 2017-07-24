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
namespace LaravelBoot\Foundation\Network\Connection\Factory;

use swoole_client as SwooleClient;
use LaravelBoot\Foundation\Contracts\ConnectionFactory;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Network\Connection\Driver\NovaClient as NovaClientConnection;

class NovaClient implements ConnectionFactory
{
    const CONNECT_TIMEOUT = 3000;

    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function create()
    {
        $clientFlags = SWOOLE_SOCK_TCP;
        $socket = new SwooleClient($clientFlags, SWOOLE_SOCK_ASYNC);
        $socket->set($this->config['config']);

        $serverInfo = isset($this->config["server"]) ? $this->config["server"] : [];
        $connection = new NovaClientConnection($serverInfo);
        $connection->setSocket($socket);
        $connection->setConfig($this->config);
        $connection->init();

        //call connect
        if (false === $socket->connect($this->config['host'], $this->config['port'])) {
            sys_error("NovaClient connect ".$this->config['host'].":".$this->config['port']. " failed");
            return null;
        }

        $connectTimeout = isset($this->config['connect_timeout']) ? $this->config['connect_timeout'] : self::CONNECT_TIMEOUT;
        Timer::after($connectTimeout, $this->getConnectTimeoutCallback($connection), $connection->getConnectTimeoutJobId());

        return $connection;
    }

    public function getConnectTimeoutCallback(NovaClientConnection $connection)
    {
        return function() use ($connection) {
            $connection->close();
            $connection->onConnectTimeout();
        };
    }

    public function close()
    {

    }

}
