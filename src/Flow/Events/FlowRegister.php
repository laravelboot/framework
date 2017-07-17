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
namespace LaravelBoot\Foundation\Flow\Events;

use Illuminate\Container\Container;
use LaravelBoot\Foundation\Flow\FlowManager;

/**
 * Class FlowRegister.
 */
class FlowRegister
{
    /**
     * @var \LaravelBoot\Foundation\Flow\FlowManager
     */
    protected $flow;

    /**
     * FlowRegister constructor.
     *
     * @param \LaravelBoot\Foundation\Flow\FlowManager $flow
     */
    public function __construct(FlowManager $flow)
    {
        $this->flow = $flow;
    }
}
