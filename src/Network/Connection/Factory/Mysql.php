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

use LaravelBoot\Foundation\Contracts\ConnectionFactory;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Network\Connection\Driver\Mysql as MysqlConnection;

class Mysql implements ConnectionFactory
{
    const DEFAULT_CHARSET = "utf8mb4";

    /**
     * @var array
     */
    private $config;

    /**
     * @var \swoole_mysql
     */
    private $conn;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function create()
    {
        $servInfo = [
            "host" => $this->config['host'],
            "port" => $this->config['port'],
            "user" => $this->config['user'],
            "password" => $this->config['password'],
            "database" => $this->config['database'],
            "charset" => isset($this->config['charset']) ? $this->config['charset'] : self::DEFAULT_CHARSET,
        ];


        $this->conn = new \swoole_mysql();
        $connection = new MysqlConnection();
        $connection->setSocket($this->conn);
        $connection->setConfig($this->config);
        $connection->init();

        $this->conn->on('close', [$connection, 'onClose']);
        $this->conn->connect($servInfo, [$connection, "onConnect"]);

        Timer::after($this->config['connect_timeout'], $this->getConnectTimeoutCallback($connection), $connection->getConnectTimeoutJobId());

        return $connection;
    }

    public function getConnectTimeoutCallback(MysqlConnection $connection)
    {
        return function() use ($connection) {
            $connection->close();
            $connection->onConnectTimeout();
        };
    }

    public function close()
    {
        $this->conn->close();
    }
}