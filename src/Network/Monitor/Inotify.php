<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/20 19:36
 * @version
 */
namespace LaravelBoot\Foundation\Network\Monitor;

use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Exception\LaravelBootException;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Utility\Singleton;
use Illuminate\Filesystem\Filesystem;

class Inotify
{
    use Singleton;

    const RELOAD_SIG = 'reload_sig';

    protected $monitor_dir;
    protected $inotifyFd;
    protected $managePid;
    private $server;

    public static $monitor_files = [];

    public function run($server,$monitor_dir)
    {
        $this->monitor_dir = $monitor_dir;
        $this->server = $server;
        if (!extension_loaded('inotify')) {
            Timer::after(1000, [$this, 'monitor'],spl_object_hash($this));
        }
    }

    public function monitor()
    {
        $this->inotifyFd = inotify_init();

        Application::getInstance()->make(Filesystem::class)->files($this->monitor_dir)->each(function($file){
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php'){
                // 把文件加入inotify监控，这里只监控了IN_MODIFY文件更新事件
                $wd = inotify_add_watch($this->inotifyFd,$file,IN_MODIFY);
                Inotify::$monitor_files[$wd] = $file;
            }
        });

        swoole_event_add($this->inotifyFd,function($inotify_fd){
            $events = inotify_read($inotify_fd);
            if($events){
                foreach($events as $ev){
                    $file = Inotify::$monitor_files[$ev['wd']];
                    unset(Inotify::$monitor_files[$ev['wd']]);
                    $wd = inotify_add_watch($this->inotifyFd,$file,IN_MODIFY);
                    Inotify::$monitor_files[$wd] = $file;
                }
                //todo:reload server config
                $this->server->reload();
            }
        },null,SWOOLE_EVENT_READ);
    }
}