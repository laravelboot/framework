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
namespace LaravelBoot\Foundation\Network\Connection\Driver;

use swoole_client as SwooleClient;
use Zan\Framework\Contract\Network\Connection;
use Zan\Framework\Network\Connection\ReconnectionPloy;
use Zan\Framework\Network\Server\Timer\Timer;

class Syslog extends Base implements Connection
{
    private $clientCb;
    protected $isAsync = true;

    public function init()
    {
        //set callback
        $this->getSocket()->on('connect', [$this, 'onConnect']);
        $this->getSocket()->on('receive', [$this, 'onReceive']);
        $this->getSocket()->on('close', [$this, 'onClose']);
        $this->getSocket()->on('error', [$this, 'onError']);
    }

    public function send($log)
    {
        /** @var \swoole_client $swClient */
        $swClient = $this->getSocket();
        $r = $swClient->send($log);
        if ($r === false) {
            sys_error("syslog send fail: $log");
        }
        $this->release();
    }

    public function onConnect($cli)
    {
        Timer::clearAfterJob($this->getConnectTimeoutJobId());
        $this->release();

        ReconnectionPloy::getInstance()->connectSuccess(spl_object_hash($this));
        sys_echo("syslog client connect to server " . $this->getConnString());
    }

    public function onClose(SwooleClient $cli)
    {
        Timer::clearAfterJob($this->getConnectTimeoutJobId());
        $this->close();
        sys_echo("syslog client close " . $this->getConnString());
    }

    public function onReceive(SwooleClient $cli, $data)
    {
        $this->release();
        call_user_func($this->clientCb, $data);
    }

    public function onError(SwooleClient $cli)
    {
        Timer::clearAfterJob($this->getConnectTimeoutJobId());
        $this->close();
        sys_error("syslog client error " . $this->getConnString());
    }

    public function setClientCb(callable $cb)
    {
        $this->clientCb = null;
    }

    public function closeSocket()
    {
        try {
            $this->getSocket()->close();
        } catch (\Throwable $t) {
            echo_exception($t);
        } catch (\Exception $e) {
            echo_exception($e);
        }
    }
}
