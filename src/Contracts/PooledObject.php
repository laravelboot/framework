<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 13:56
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

abstract class PooledObject
{
    public function isAlive()
    {
        return true;
    }
}