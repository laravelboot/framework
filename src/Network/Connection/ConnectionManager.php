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
namespace LaravelBoot\Foundation\Network\Connection;


use LaravelBoot\Foundation\Contracts\Connection;
use LaravelBoot\Foundation\Network\Common\Condition;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Exception\ConditionException;
use LaravelBoot\Foundation\Exception\InvalidArgumentException;
use LaravelBoot\Foundation\Network\Connection\Exception\CanNotCreateConnectionException;
use LaravelBoot\Foundation\Network\Connection\Exception\ConnectTimeoutException;
use LaravelBoot\Foundation\Network\Connection\Exception\GetConnectionTimeoutFromPool;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Network\Monitor\Constant;
use LaravelBoot\Foundation\Network\Monitor\Hawk;
use LaravelBoot\Foundation\Utility\Singleton;

class ConnectionManager
{

    use Singleton;

    /**
     * @var Pool[]
     */
    private static $poolMap = [];

    /**
     * @var PoolEx[]
     */
    private static $poolExMap = [];

    private static $server;

    public static $getPoolEvent = "getPoolEvent";

    /**
     * @param string $connKey
     * @return \LaravelBoot\Foundation\Contracts\Connection
     * @throws InvalidArgumentException | CanNotCreateConnectionException | ConnectTimeoutException
     */
    public function get($connKey)
    {
        while (!isset(self::$poolMap[$connKey]) && !isset(self::$poolExMap[$connKey])) {
            try {
                yield new Condition(static::$getPoolEvent, 300);
            } catch (ConditionException $e) {
                sys_error("poolName: $connKey condition wait timeout");
            }
        }

        if (isset(self::$poolExMap[$connKey])) {
            yield $this->getFromPoolEx($connKey);
        } else if(isset(self::$poolMap[$connKey])){
            yield $this->getFromPool($connKey);
        } else {
            throw new InvalidArgumentException('No such ConnectionPool:'. $connKey);
        }
    }

    private function getFromPool($connKey)
    {
        $pool = self::$poolMap[$connKey];

        $conf = $pool->getPoolConfig();
        $maxWaitNum = $conf['pool']['maximum-wait-connection'];
        if ($pool->waitNum > $maxWaitNum) {
            throw new CanNotCreateConnectionException("Connection $connKey has up to the maximum waiting connection number");
        }

        $connection = (yield $pool->get());

        if ($connection instanceof Connection) {
            yield $connection;
        } else {
            yield new FutureConnection($this, $connKey, $conf["connect_timeout"], $pool);
        }
    }

    private function getFromPoolEx($connKey)
    {
        $pool = self::$poolExMap[$connKey];
        $connection = (yield $pool->get());

        if ($connection instanceof Connection) {
            yield $connection;
        } else {
            throw new GetConnectionTimeoutFromPool("get connection $connKey timeout");
        }
    }

    /**
     * @param $poolKey
     * @param Pool|PoolEx $pool
     * @throws InvalidArgumentException
     */
    public function addPool($poolKey, $pool)
    {
        if ($pool instanceof Pool) {
            self::$poolMap[$poolKey] = $pool;
        } else if ($pool instanceof PoolEx) {
            self::$poolExMap[$poolKey] = $pool;
        } else {
            throw new InvalidArgumentException("invalid pool type, poolKey=$poolKey");
        }
    }

    public function monitor()
    {
        Timer::tick(30 * 1000, function() {
            foreach (self::$poolExMap as $poolKey => $pool) {
                $info = $pool->getStatInfo();
                $all = $info["all"];
                $free = $info["free"];
                sys_echo("pool_ex info [type=$poolKey, all=$all, free=$free]");
            };
        });

        $config = Config::get('hawk');
        if (!$config['run']) {
            return;
        }
        $time = $config['time'];
        Timer::tick($time, [$this, 'monitorTick']);
    }

    public function monitorTick()
    {
        $hawk = Hawk::getInstance();

        foreach (self::$poolMap as $poolKey => $pool) {
            $activeNums = $pool->getActiveConnection()->length();
            $freeNums = $pool->getFreeConnection()->length();

            $hawk->add(Constant::BIZ_CONNECTION_POOL,
                [
                    'free'  => $freeNums,
                    'active' => $activeNums,
                ], [
                    'pool_name' => $poolKey,
                ]
            );
        }

        foreach (self::$poolExMap as $poolKey => $pool) {
            $hawk->add(Constant::BIZ_CONNECTION_POOL, $pool->getStatInfo(), [
                    'pool_name' => $poolKey,
                ]
            );
        }
    }

    public function setServer($server)
    {
        self::$server = $server;
    }

    public function monitorConnectionNum()
    {
        MonitorConnectionNum::getInstance()->controlLinkNum(self::$poolMap);
    }
}
