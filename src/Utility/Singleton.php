<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/19 10:28
 * @version
 */
namespace LaravelBoot\Foundation\Utility;

trait Singleton
{
    private static $_instance = null;

    final public static function getInstance()
    {
        if(null === static::$_instance){
            static::$_instance = new static();
        }
        return static::$_instance;
    }
}