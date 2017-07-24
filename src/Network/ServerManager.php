<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/21 13:27
 * @version
 */
namespace LaravelBoot\Foundation\Network;

use LaravelBoot\Foundation\Utility\Singleton;
use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Network\Server\Factory as ServerFactory;

class ServerManager
{
    use Singleton;

    public function start($host,$port,$daemon,$config,$mode='http')
    {
        $factory = new ServerFactory('server');
        if($mode=='http'){
            $server = $factory->createHttpServer($host,$port,$daemon,$config);
        }elseif($mode=='tcp'){
            $server = $factory->createTcpServer($host,$port,$daemon,$config);
        }elseif($mode=='websock'){
            $server = $factory->createWebSocketServer($host,$port,$daemon,$config);
        }
        $server->start();
    }

    public function stop()
    {
        $pidFilePath = Application::getInstance()->storagePath() . '/run/' . strtolower(Application::getInstance()->getName()) . '.pid';
        if(file_exists($pidFilePath)){
            $pid = file_get_contents($pidFilePath);
            posix_kill($pid,SIGTERM);
            $timeout = 60;
            $start_time = time();
            while(true){
                $master_is_alive = $pid && posix_kill($pid,0);
                if($master_is_alive){
                    if(time()-$start_time >= $timeout){
                        exec('ps -ef|grep '.Application::getInstance()->getName().'|grep -v grep|cut -c 9-15|xargs kill -9');
                        sys_error('server force stop');
                        exit;
                    }
                    sleep(1);
                    continue;
                }
                sys_echo('server stop success');
                break;
            }
        }
    }

    public function restart($host,$port,$daemon,$config,$mode='http')
    {
        $this->stop();
        sleep(3);
        $this->start($host,$port,$daemon,$config,$mode);
    }

    public function reload()
    {

    }
}