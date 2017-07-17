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
 * Trait Logable.
 */
trait Logable
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function log()
    {
        return $this->container->make('log');
    }
}
