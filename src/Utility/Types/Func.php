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
namespace LaravelBoot\Foundation\Utility\Types;

use LaravelBoot\Foundation\Exception\InvalidArgumentException;

class Func
{
    public static function toClosure(callable $func, $args=null, callable $validator=null)
    {
        if(!is_callable($func)){
            throw new InvalidArgumentException('Func::toClousure first args must be callable');
        }

        return function() use ($func, $args, $validator) {
            if(null !== $validator) {
                $validArgs = func_get_args();
                if(!Func::call($validator, $validArgs)){
                    return null;
                }
            }
            
            return Func::call($func, $args);
        };
    }
    
    public static function call(callable $func, $args=null)
    {
        if(!is_callable($func)){
            throw new InvalidArgumentException('Func::call first args must be callable');
        }
        
        if(null===$args){
            return call_user_func($func);
        }

        if(!is_array($args)){
            $args = [$args];
        }

        return call_user_func_array($func, $args);
    }

}
