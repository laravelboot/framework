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

use Illuminate\Container\Container;

class InitializeServiceChain
{
    /**
     * @param $server
     * @param $workerId
     */
    public function bootstrap($server, $workerId)
    {
        $container = Container::getInstance();
    }
}