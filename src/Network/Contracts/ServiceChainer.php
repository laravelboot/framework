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
namespace LaravelBoot\Foundation\Network\Contracts;

interface ServiceChainer
{
    const TYPE_HTTP = 1;
    const TYPE_TCP  = 2;
    const TYPE_JOB  = 3;

    public function getChainKey($type);

    /**
     * get service chain value by type
     * @param $type
     * @param array $ctx
     * @return mixed
     */
    public function getChainValue($type, array $ctx = []);

    /**
     * get endpoint by app-name [and service chain key]
     * @param string $appName
     * @param string $scKey
     * @return array|null
     * scKey === null
     * [
     *  scKey1 => ["$host:$port" => list($host, $port), ...],
     *  ...
     * ]
     * scKey !== null
     * ["$host:$port" => list($host, $port), ...],
     */
    public function getEndpoints($appName, $scKey = null);
}