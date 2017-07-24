<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 15:01
 * @version
 */
namespace LaravelBoot\Foundation\Coroutine;

class SysCall
{
    protected $callback = null;

    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke(Task $task)
    {
        return call_user_func($this->callback, $task);
    }
}