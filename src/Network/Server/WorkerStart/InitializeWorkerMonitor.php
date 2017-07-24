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

use LaravelBoot\Foundation\Network\Contracts\Bootable;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Network\Server\Monitor\Worker;

class InitializeWorkerMonitor implements Bootable
{
    public function bootstrap($server)
    {
        $config = Config::get('server.monitor');
        Worker::getInstance()->init($server,$config);
    }

}