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

class ConnectionEx implements Connection
{
    public $connEx;
    public $poolEx;
    private $isReleased;

    public function __construct($connEx, PoolEx $poolEx)
    {
        $this->connEx = $connEx;
        $this->poolEx = $poolEx;
        $this->isReleased = false;
    }

    public function getSocket()
    {
        return $this->connEx;
    }

    public function getEngine()
    {
        return $this->poolEx->poolType;
    }

    public function getConfig()
    {
        return $this->poolEx->config;
    }

    public function release()
    {
        return $this->releaseOnce();
    }

    public function close()
    {
        return $this->releaseOnce(true);
    }

    public function heartbeat() { }


    private function releaseOnce($close = false)
    {
        if ($this->isReleased) {
            return false;
        }

        $this->isReleased = true;
        return $this->poolEx->release($this->connEx, $close);
    }
}