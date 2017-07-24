<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 13:56
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface ConnectionPool
{

    /**
     * ConnectionPool constructor.
     * @param ConnectionFactory $connectionFactory
     * @param array $config
     * @param $type
     */
    public function __construct(ConnectionFactory $connectionFactory, array $config, $type);

    /**
     * @param array $config
     * @return bool
     */
    public function reload(array $config);

    /**
     * @return Connection
     * @TODO 服务器宕机处理???
     */
    public function get();

    /**
     * @param Connection $conn
     * @return bool
     */
    public function remove(Connection $conn);

    /**
     * @param Connection $conn
     * @return bool
     */
    public function recycle(Connection $conn);

}