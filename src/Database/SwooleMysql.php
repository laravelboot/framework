<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/20 15:01
 * @version
 */
namespace LaravelBoot\Foundation\Database;

use LaravelBoot\Foundation\Contracts\Async;
use LaravelBoot\Foundation\Contracts\Connection;
use LaravelBoot\Foundation\Database\MysqliConnectionLostException;
use LaravelBoot\Foundation\Database\MysqliQueryDuplicateEntryUniqueKeyException;
use LaravelBoot\Foundation\Database\MysqliQueryException;
use LaravelBoot\Foundation\Database\MysqliQueryTimeoutException;
use LaravelBoot\Foundation\Database\MysqliSqlSyntaxException;
use LaravelBoot\Foundation\Database\MysqliTransactionException;


class SwooleMysql implements Async
{
    /**
     * @var \LaravelBoot\Foundation\Network\Connection\Driver\Mysql
     */
    private $connection;

    private $sql;

    /**
     * @var callable
     */
    private $callback;

    private $result;

    private $countAlias;

    /** @var \swoole_mysql $swooleMysql */
    private $swooleMysql;

    const DEFAULT_QUERY_TIMEOUT = 3000;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->swooleMysql = $connection->getSocket();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setCountAlias($countAlias)
    {
        $this->countAlias = $countAlias;
    }

    public function getCountAlias()
    {
        return $this->countAlias;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function execute($callback, $task)
    {
        $this->callback = $callback;
    }

    /**
     * @param $sql
     * @return \Generator
     * @throws MysqliQueryException
     */
    public function query($sql)
    {
        $this->sql = $sql;
        $r = $this->swooleMysql->query($this->sql, [$this, "onSqlReady"]);
        if ($r === false) {
            throw new MysqliQueryException("mysql query fail [sql=$this->sql]");
        } else {
            $this->beginTimeoutTimer("mysql query");
            yield $this;
        }
    }

    public function beginTransaction($flags = 0)
    {
        yield $this->query("START TRANSACTION");
    }

    public function commit($flags = 0)
    {
        yield $this->query("COMMIT");
    }

    public function rollback($flags = 0)
    {
        yield $this->query("ROLLBACK");
    }

    /**
     * @param \swoole_mysql $link
     * @param array|bool $result
     * @return void|\LaravelBoot\Foundation\Database\DbResultInterface
     * @throws MysqliConnectionLostException
     * @throws MysqliQueryDuplicateEntryUniqueKeyException
     * @throws MysqliQueryException
     * @throws MysqliSqlSyntaxException
     */
    public function onSqlReady($link, $result = true)
    {
        $this->cancelTimeoutTimer();

        $exception = null;

        if ($result === false) {

            $errno = $link->errno;
            $error = $link->error;
            if (in_array($errno, [2013, 2006])) {
                $exception = new MysqliConnectionLostException("$error:$this->sql");
            } elseif ($errno == 1064) {
                $exception = new MysqliSqlSyntaxException("$error:$this->sql");
            } elseif ($errno == 1062) {
                $exception = new MysqliQueryDuplicateEntryUniqueKeyException("$error:$this->sql");
            } else {
                $ctx = [
                    'sql' => $this->sql,
                    'errno' => $errno,
                    'error' => $error,
                ];
                $exception = new MysqliQueryException("errno=$errno&error=$error:$this->sql", 0, null, $ctx);
            }
        } else {

        }

        $this->result = $result;

        if ($this->callback) {
            $callback = $this->callback;
            $callback(new MysqliResult($this), $exception);
            $this->callback = null;
        }
    }

    private function beginTimeoutTimer($type)
    {
        $config = $this->connection->getConfig();
        $timeout = isset($config['timeout']) ? $config['timeout'] : self::DEFAULT_QUERY_TIMEOUT;
        Timer::after($timeout, $this->onQueryTimeout($this->sql, $type), spl_object_hash($this));
    }

    private function cancelTimeoutTimer()
    {
        Timer::clearAfterJob(spl_object_hash($this));
    }

    private function onQueryTimeout($sql, $type)
    {
        $start = microtime(true);
        return function() use($sql, $start, $type) {
            if ($this->callback) {
                $duration = microtime(true) - $start;
                $ctx = [
                    "sql" => $sql,
                    "duration" => $duration,
                ];
                $callback = $this->callback;
                $ex = new MysqliQueryTimeoutException("Mysql $type timeout [sql=$sql, duration=$duration]", 0, null, $ctx);
                $callback(null, $ex);
            }
        };
    }
}