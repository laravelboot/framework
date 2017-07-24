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

class MysqliResult implements DbResultInterface
{
    /**
     * @var Mysql
     */
    private $driver;

    /**
     * FutureResult constructor.
     * @param DriverInterface $driver
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        $insertId = $this->driver->getConnection()->getSocket()->insert_id;
        yield $insertId;
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        $affectedRows = $this->driver->getConnection()->getSocket()->affected_rows;
        yield $affectedRows;
    }

    /**
     * @return array
     */
    public function fetchRows()
    {
        yield $this->driver->getResult();
    }

    public function getCountRows()
    {
        $rows = (yield $this->fetchRows());
        $countAlias = $this->driver->getCountAlias();
        yield !isset($rows[0][$countAlias]) ? 0 : (int)$rows[0][$countAlias];
    }
}