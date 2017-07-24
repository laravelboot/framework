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
use LaravelBoot\Foundation\Network\Monitor\Hawk;

class InitializeHawkMonitor implements Bootable
{
    public function bootstrap($server)
    {
        Hawk::getInstance()->run($server);
    }

}