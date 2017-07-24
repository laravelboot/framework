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
use Zan\Framework\Network\ServerManager\ServerRegister;
use Zan\Framework\Network\ServerManager\ServerRegisterInitiator;

class InitializeEtcdTTLRefreshing implements Bootable
{
    public function bootstrap($server)
    {
        $workerId = func_get_arg(1);
        if ($workerId === 0) {
            $enableRegister = ServerRegisterInitiator::getInstance()->getRegister();
            if ($enableRegister) {
                $sr = ServerRegisterInitiator::getInstance();
                $serverRegister = new ServerRegister();

                $configs = $sr->createRegisterConfigs();
                foreach ($configs as $config) {
                    $serverRegister->refreshingEtcdV2TTL($config);
                }
            }
        }
    }
}