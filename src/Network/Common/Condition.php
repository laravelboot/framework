<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/20 14:38
 * @version
 */
namespace LaravelBoot\Foundation\Network\Common;

use LaravelBoot\Foundation\Contracts\Async;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Network\Common\Exception\ConditionException;

class Condition implements Async
{
    private $taskCallback = null;
    private $evtName;
    private $timerSet = false;

    public function __construct($evtName,$timeout = null)
    {
        if(is_integer($timeout) && $timeout > 0){
            $this->timerSet = true;
            Timer::after($timeout,[$this,"onTimeout"],$this->getConditionTimeoutJobId());
        }
        Event::once($evtName,[$this,"onEvent"]);
        $this->evtName = $evtName;
    }

    public function onEvent()
    {
        if($this->timerSet) Timer::clearAfterJob($this->getConditionTimeoutJobId());
        if($this->taskCallback) call_user_func($this->taskCallback,true);
    }

    public static function wakeUp($evtName)
    {
        Event::fire($evtName);
    }

    public function onTimeout()
    {
        Event::unbind($this->evtName,[$this,'onEvent']);
        if($this->taskCallback) call_user_func($this->taskCallback,null,new ConditionException("condition {$this->evtName} timeout"));
    }

    public function execute($callback,$task)
    {
        $this->taskCallback = $callback;
    }

    private function getConditionTimeoutJobId()
    {
        return spl_object_hash($this) . '_condition_timeout';
    }
}