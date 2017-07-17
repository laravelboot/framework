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
namespace LaravelBoot\Foundation\Routing\Traits;

/**
 * Trait Flowable.
 */
trait Flowable
{
    /**
     * @return \LaravelBoot\Foundation\Flow\FlowManager
     */
    protected function flow()
    {
        return $this->container->make('flow');
    }
}
