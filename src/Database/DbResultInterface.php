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

interface DbResultInterface
{
    /**
     * FutureResult constructor.
     * @param $driver
     */
    public function __construct($driver);

    /**
     * @return int
     */
    public function getLastInsertId();

    /**
     * @return int
     */
    public function getAffectedRows();

    /**
     * @return array
     */
    public function fetchRows();
}