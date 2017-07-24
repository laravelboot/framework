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
namespace LaravelBoot\Foundation\Network\Common;


use Zan\Framework\Foundation\Contract\Async;
use Kdt\Iron\Nova\Exception\NetworkException;
use Zan\Framework\Network\Common\Exception\TcpSendErrorException;
use Zan\Framework\Network\Common\Exception\TcpSendTimeoutException;
use Zan\Framework\Network\Connection\ConnectionEx;

class TcpClientEx implements Async
{
    const DEFAULT_SEND_TIMEOUT = 3000;

    /**
     * @var ConnectionEx
     */
    private $connEx;

    /**
     * @var \swoole_client
     */
    private $sock;

    /**
     * @var callable
     */
    private $callback;

    private $hasRecv;

    private $config;

    public function __construct(ConnectionEx $conn)
    {
        $this->connEx = $conn;
        $this->sock = $conn->getSocket();
        $this->config = $conn->getConfig();

        if (isset($this->config['hasRecv']) && $this->config['hasRecv'] === false) {
            $this->hasRecv = false;
        } else {
            $this->hasRecv = true;
        }
    }

    public function execute(callable $callback, $task)
    {
        $this->callback = $callback;
    }

    private function sendWithRecv($data)
    {
        $sendTimeout = isset($this->config["timeout"]) ? $this->config["timeout"] : static::DEFAULT_SEND_TIMEOUT;

        $this->sock->setSendTimeout($sendTimeout);
        $this->sock->on("timeout", [$this, "recv"]);
        $this->sock->on("receive", [$this, "recv"]);
        $sent = $this->sock->send($data);

        if ($sent === false) {
            $this->connEx->close();
            throw new NetworkException("TCP client send fail");
        }

        yield $this;
    }

    private function sendWithoutRecv($data)
    {
        $sent = $this->sock->send($data);

        if ($sent === false) {
            $this->connEx->close();
            throw new NetworkException("TCP client send fail");
        } else {
            $this->connEx->release();
        }
    }

    public function send($data)
    {
        if ($this->hasRecv) {
            yield $this->sendWithRecv($data);
        } else {
            $this->sendWithoutRecv($data);
        }
    }

    public function recv(\swoole_client $client, $r)
    {
        if (is_int($r)) {
            $this->connEx->close();
            $ex = new TcpSendTimeoutException("TCP client send timeout, type=$r");
            call_user_func($this->callback, $r, $ex);
        } else if ($r === false || $r === "") {
            $this->connEx->close();
            $ex = new TcpSendErrorException(socket_strerror($this->sock->errCode), $this->sock->errCode);
            call_user_func($this->callback, $r, $ex);
        } else {
            $this->connEx->release();
            call_user_func($this->callback, $r);
        }
    }
}