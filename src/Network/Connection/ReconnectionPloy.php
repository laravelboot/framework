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

class ReconnectionPloy
{

    use Singleton;

    private $config = [];

    private $reconnectTime = [];

    public function init()
    {
        $this->config = Config::get('reconnection');
    }

    public function reconnect($conn,$pool)
    {
        if($pool instanceof Pool){
            $poolConf = $pool->getPoolConfig();
            $maxWaitNum = $poolConf['pool']['maximum-wait-connection'];
            if($pool->waitNum > $maxWaitNum){
                return;
            }
        }

        $connHashCode = spl_object_hash($conn);
        $intervalTime = isset($this->config['interval-reconnect-time']) ? $this->config['interval-reconnect-time'] : 5000;
        $maxTime = isset($this->config['max-reconnect-time']) ? $this->config['max-reconnect-time'] : 30000;
        $this->reconnectTime[$connHashCode] = ($this->reconnectTime[$connHashCode] + $intervalTime) >= $maxTime ? $maxTime : ($this->reconnectTime[$connHashCode] + $intervalTime);
        Timer::after($this->reconnectTime[$connHashCode],function () use ($pool,$connHashCode,$conn) {
            $pool->createConnect($connHashCode,$conn);
        });
    }

    public function connectSuccess($key)
    {
        unset($this->reconnectTime[$key]);
    }

    public function getReconnectTime($key)
    {
        if(isset($this->reconnectTime[$key])){
            return $this->reconnectTime[$key];
        } else{
            return null;
        }
    }

    public function setReconnectTime($key,$value)
    {
        $this->reconnectTime[$key] = $value;
    }
}