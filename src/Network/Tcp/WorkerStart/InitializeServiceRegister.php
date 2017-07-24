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
namespace LaravelBoot\Foundation\Network\Tcp\WorkerStart;

use LaravelBoot\Foundation\ServiceManager\ServerRegisterInitiator;

class InitializeServiceRegister
{
    /**
     * @param
     */
    public function bootstrap($server)
    {
        ServerRegisterInitiator::getInstance()->init();
    }
}