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

use LaravelBoot\Foundation\Contracts\Async;
use LaravelBoot\Foundation\Network\Common\Exception\DnsLookupTimeoutException;
use LaravelBoot\Foundation\Utility\Timer;

class DnsClient implements Async
{
    const maxRetryCount = 3;

    private $callback;
    private $host;
    private $count;
    private $timeoutFn;
    private $timeout;

    public static function lookup($host, $callback = null, $timeoutFn = null, $timeout = 100)
    {
        $self = new static;
        $self->host = $host;
        $self->callback = $callback;
        $self->timeoutFn = $timeoutFn;
        $self->count = 0;
        if ($timeout <= 0)
            $timeout = 100;
        $self->timeout = $timeout;
        $self->resolve();
        return $self;
    }

    public static function lookupWithoutTimeout($host, $callback)
    {
        swoole_async_dns_lookup($host, $callback);
    }

    public function resolve()
    {
        $this->onTimeout($this->timeout);
        // 无需做缓存, 内部有缓存
        swoole_async_dns_lookup($this->host, function($host, $ip) {
            Timer::clearAfterJob($this->timerId());
            if ($this->callback) {
                call_user_func($this->callback, $host, $ip);
            }
        });
    }


    public function onTimeout($duration)
    {
        if ($this->count < self::maxRetryCount) {
            Timer::after($duration, [$this, "resolve"], $this->timerId());
            $this->count++;
        } else {
            Timer::after($duration, function() {
                if ($this->timeoutFn) {
                    call_user_func($this->timeoutFn);
                }
            }, $this->timerId());
        }
    }

    private function timerId()
    {
        return spl_object_hash($this) . "_dns_lookup";
    }

    public function execute($callback, $task)
    {
        $this->callback = function ($host, $ip) use ($callback) {
            if (empty($ip)) {
                call_user_func($callback, null, new DnsLookupTimeoutException("dns lookup $host failed"));
            } else {
                call_user_func($callback, $ip);
            }
        };

        $this->timeoutFn = function () use ($callback) {
            call_user_func($callback, null, new DnsLookupTimeoutException("dns lookup {$this->host} timeout"));
        };

        $this->resolve();
    }

    /*
     * 协程调度专用接口
     */
    public function query($host, $timeout = 100)
    {
        $this->host = $host;
        if ($timeout <= 0)
            $timeout = 100;
        $this->timeout = $timeout;
        yield $this;
    }
}