<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 13:58
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface PooledObjectFactory
{
    public function create();
    public function destroy(PooledObject $object);
}