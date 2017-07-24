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

use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Utility\Singleton;
use LaravelBoot\Foundation\Utility\Types\Time;

class MonitorConnectionNum {

    use Singleton;

    private static $poolMap=[];

    public function controlLinkNum($poolMap)
    {
        self::$poolMap = $poolMap;
        $config = Config::get('reconnection');
        $time = isset($config['interval-reduce-link'])?  $config['interval-reduce-link'] : 60000;
        Timer::tick($time, [$this, 'reduceLinkNum']);
    }

    public function reduceLinkNum()
    {
        $config = Config::get('reconnection');
        $timeInterval = isset($config['interval-reduce-link'])?  $config['interval-reduce-link'] : 60000;
        foreach (self::$poolMap as $poolKey => $pool) {
            $activeNum = $pool->getActiveConnection()->length();
            $freeNum = $pool->getFreeConnection()->length();
            $sumNum = $activeNum + $freeNum;
            if ($sumNum <=0) {
                continue;
            }
            $poolConfig = Config::get('connection.' . $poolKey)['pool'];
            $minConnectionNum = isset($poolConfig['minimum-connection-count']) ? $poolConfig['minimum-connection-count'] : 1;
            if ($freeNum <= $minConnectionNum) {
                continue;
            }
            for ($i=0; $i<$freeNum-$minConnectionNum; $i++) {
                $conn = $pool->getFreeConnection()->pop();
                if ($conn->lastUsedTime == 0 || (Time::current(true) - $conn->lastUsedTime) > $timeInterval/1000) {
                    $conn->closeSocket();
                } else {
                    $pool->getFreeConnection()->push($conn);
                }
            }
        }
    }
}