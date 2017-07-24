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

use LaravelBoot\Foundation\ServiceManager\ServiceDiscoveryInitiator;

class InitializeServiceDiscovery
{
    /**
     * @param $server
     * @param $workerId
     */
    public function bootstrap($server, $workerId)
    {
        ServiceDiscoveryInitiator::getInstance()->init($workerId);
    }
}