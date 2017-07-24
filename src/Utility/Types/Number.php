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

class Number
{
    public static function floatToString($float) /* string */
    {
        if(is_string($float)) {
            return $float;
        }

        if(!is_float($float)) {
            throw new InvalidArgumentException('invalid argument for Number::floatToString(' . $float . ')');
        }

        $string = (string) $float;
        $string = str_replace('.', '', $string);
        $string = ltrim($string, '0');

        return $string;
    }
}