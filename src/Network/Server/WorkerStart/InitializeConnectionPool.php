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
namespace LaravelBoot\Foundation\Network\Server\WorkerStart;

use LaravelBoot\Foundation\Network\Connection\ConnectionInitiator;
use LaravelBoot\Foundation\Network\Contracts\Bootable;

class InitializeConnectionPool implements Bootable
{
    public function bootstrap($server)
    {
        ConnectionInitiator::getInstance()->init('connection', $server);
    }
}