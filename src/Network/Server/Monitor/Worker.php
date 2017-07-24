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
namespace LaravelBoot\Foundation\Network\Server\Monitor;

use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Coroutine\Task;
use LaravelBoot\Foundation\Network\Monitor\Constant;
use LaravelBoot\Foundation\Network\Monitor\Hawk;
use LaravelBoot\Foundation\Utility\Singleton;
use LaravelBoot\Foundation\Network\Http\Server;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Utility\Types\Time;


class Worker
{
    use Singleton;

    const GAP_TIME = 180000;
    const GAP_REACTION_NUM = 1500;
    const GAP_MSG_NUM = 5000;
    const DEFAULT_MAX_CONCURRENCY = 500;

    public $classHash;
    public $workerId;
    public $server;
    public $config;

    public $reactionNum;
    public $totalReactionNum;
    public $maxConcurrency;

    private $totalMsgNum = 0;
    private $checkMqReadyClose;
    private $mqReadyClosePre;

    private $isDenyRequest;

    public function init($server,$config)
    {
        if(!is_array($config)){
            return;
        }

        $this->isDenyRequest = false;
        $this->classHash = spl_object_hash($this);
        $this->server = $server;
        $this->workerId = $server->swooleServer->worker_id;
        $this->config = $config;
        $this->reactionNum = 0;
        $this->totalReactionNum = 0;
        $this->maxConcurrency = isset($this->config['max_concurrency']) ? $this->config['max_concurrency'] : self::DEFAULT_MAX_CONCURRENCY;

        $this->restart();
        $this->checkStart();
        $this->hawk();
    }

    public function restart()
    {
        $time = isset($this->config['max_live_time']) ? $this->config['max_live_time'] : 1800000;
        $time += $this->workerId * self::GAP_TIME;

        Timer::after($time,[$this,'closePre'],$this->classHash . '_restart');
    }

    public function checkStart()
    {
        $time = isset($this->config['check_interval']) ? $this->config['check_interval'] : 5000;

        Timer::tick($time,[$this,'check'],$this->classHash . '_check');
    }

    public function check()
    {
        $this->output('check');

        $memory = memory_get_usage();
        $memory_limit = isset($this->config['memory_limit']) ? $this->config['memory_limit'] : 1024 * 1024 * 1024 * 1.5;

        $reaction_limit = isset($this->config['max_request']) ? $this->config['max_request'] : 100000;
        $reaction_limit = $reaction_limit + $this->workerId * self::GAP_REACTION_NUM;

        $msgLimit = isset($this->config['msg_limit']) ? $this->config['msg_limit'] : 100000;
        $msgLimit = $msgLimit + $this->workerId * self::GAP_MSG_NUM;

        if($memory > $memory_limit || $this->totalReactionNum > $reaction_limit || $this->totalMsgNum > $msgLimit){
            $this->closePre();
        }
    }


    public function closePre()
    {
        $this->output('ClosePre');

        Timer::clearTickJob($this->classHash . '_check');

        // TODO: 兼容zan接口修改, 全部迁移到连接池版本swoole后移除
        /* @var $this ->server Server */
        /*
        if(method_exists($this->server->swooleServer,"denyRequest")){
            $this->server->swooleServer->denyRequest($this->workerId);
        } else{
            $this->server->swooleServer->deny_request($this->workerId);
        }
        */
        $this->isDenyRequest = true;

        if(is_callable($this->mqReadyClosePre)){
            call_user_func($this->mqReadyClosePre);
        }

        $this->closeCheck();
    }

    public function closeCheck()
    {
        $this->output('CloseCheck');

        $ready = is_callable($this->checkMqReadyClose) ? call_user_func($this->checkMqReadyClose) : true;

        if($this->reactionNum > 0 or !$ready){
            Timer::after(1000,[$this,'closeCheck']);
        } else{
            $this->close();
        }
    }

    public function close()
    {
        $this->output('Close');
        sys_echo("close:workerId->" . $this->workerId);
    }

    public function hawk()
    {
        $run = Config::get('hawk.run');
        if(!$run){
            return;
        }
        $time = Config::get('hawk.time');
        Timer::tick($time,[$this,'callHawk']);
    }

    public function callHawk()
    {
        $hawk = Hawk::getInstance();
        $memory = memory_get_usage();
        $hawk->add(Constant::BIZ_WORKER_MEMORY,['used' => $memory]);
    }

    public function reactionReceive()
    {
        //触发限流
        if($this->reactionNum > $this->maxConcurrency){
            return false;
        }
        $this->totalReactionNum++;
        $this->reactionNum++;
        return true;
    }

    public function reactionRelease()
    {
        $this->reactionNum--;
    }

    public function incrMsgCount()
    {
        $this->totalMsgNum++;
    }

    public function setCheckMqReadyCloseCallback(callable $callback)
    {
        $this->checkMqReadyClose = $callback;
    }

    public function setMqReadyClosePreCallback(callable $callback)
    {
        $this->mqReadyClosePre = $callback;
    }

    public function output($str)
    {
        if(isset($this->config['debug']) && true == $this->config['debug']){
            $output = "###########################\n";
            $output .= $str . ":workerId->" . $this->workerId . "\n";
            $output .= 'time:' . time() . "\n";
            $output .= "request number:" . $this->reactionNum . "\n";
            $output .= "total request number:" . $this->totalReactionNum . "\n";
            $output .= "###########################\n\n";
            echo $output;
        }
    }

    /**
     * @return bool
     */
    public function isDenyRequest()
    {
        return $this->isDenyRequest;
    }

}