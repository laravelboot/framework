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

use LaravelBoot\Foundation\Contracts\Async;
use LaravelBoot\Foundation\Exception\LaravelBootException;
use LaravelBoot\Foundation\Network\Connection\Exception\GetConnectionTimeoutFromPool;

class AsyncConnection implements Async
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var PoolEx
     */
    private $poolEx;

    public function __construct(PoolEx $poolEx)
    {
        $this->poolEx = $poolEx;
    }

    public function __invoke(\swoole_connpool $pool, $connEx)
    {
        if ($cc = $this->callback) {
            if ($connEx === false) { // 暂时返回false只有超时的情况
                $free = $pool->getStatInfo()["idle_conn_obj"];
                if ($free !== 0) {
                    sys_error("Internal error in connection pool [pool={$this->poolEx->poolType}, free=$free]");
                }
                $cc(null, new GetConnectionTimeoutFromPool("get connection timeout [pool={$this->poolEx->poolType}]"));
            } else {
                $cc(new ConnectionEx($connEx, $this->poolEx));
            }
            $this->callback = null;
        } else {
            // swoole 内部发生同步call异步回调, 不应该发生
            $cc(null, new LaravelBootException("Internal error in connection pool [pool={$this->poolEx->poolType}]"));
        }
    }

    public function execute($callback, $task)
    {
        $this->callback = $callback;
    }
}