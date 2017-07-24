<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/21 10:42
 * @version
 */
namespace LaravelBoot\Foundation\Network\Commands;

use Illuminate\Console\Command;
use LaravelBoot\Foundation\Network\ServerManager;

class ServerCommand extends Command
{
    protected $name = 'network:server';

    protected $signature = 'network:server {op} {--host=0.0.0.0} {--port=9501} {--daemon=true} {--config} {--mode=http}';

    protected $description = 'NetworkServer Manager:start/stop/restart/reload';

    public function fire()
    {
        $op = $this->argument('op');
        $allow_op = ['start','stop','restart','reload'];
        $host   = $this->option('host');
        $port   = $this->option('port');
        $daemon = $this->option('daemon');
        $config = $this->option('config');
        $mode = $this->option('mode','http');
        if(in_array($op,$allow_op)){
            switch($op){
                case 'start':
                    ServerManager::getInstance()->start($host,$port,$daemon,$config,$mode);
                    break;
                case 'stop':
                    ServerManager::getInstance()->stop();
                    break;
                case 'restart':
                    ServerManager::getInstance()->restart($host,$port,$daemon,$config,$mode);
                    break;
                case 'reload':
                    ServerManager::getInstance()->reload();
                    break;
            }
        }else{
            $this->info('server command is not allowed.');
        }
    }
}