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
use LaravelBoot\Foundation\Network\Connection\Driver\Syslog as SyslogDriver;

class Syslog implements ConnectionFactory
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function create()
    {
        $socket = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $connection = new SyslogDriver();
        $connection->setSocket($socket);
        $connection->setConfig($this->config);
        $connection->init();

        //call connect
        if (false === $socket->connect($this->config['host'], $this->config['port']))
        {
            sys_error("Syslog connect ".$this->config['host'].":".$this->config['port']." failed");
            return null;
        }

        Timer::after($this->config['connect_timeout'], $this->getConnectTimeoutCallback($connection), $connection->getConnectTimeoutJobId());

        return $connection;
    }

    public function getConnectTimeoutCallback(SyslogDriver $connection)
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
