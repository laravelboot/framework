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
namespace LaravelBoot\Foundation\Network\Server\Middleware;

use LaravelBoot\Foundation\Contracts\Request;
use LaravelBoot\Foundation\Contracts\RequestTerminator;
use LaravelBoot\Foundation\Contracts\Context;

class AsyncTaskTerminator implements RequestTerminator
{

    public function terminate(Request $request,$response, Context $context)
    {
        $callbacks = $context->get('async_task_queue');
        if (empty($callbacks)) {
            yield null;
            return;
        }
        for ($i = 0, $l = count($callbacks); $i < $l; $i++) {
            if (is_callable($callbacks[$i])) {
                yield call_user_func($callbacks[$i]);
            }
        }
        $context->set('async_task_queue', []);
        yield null;
    }
}
