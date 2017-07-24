<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:56
 * @version
 */
namespace LaravelBoot\Foundation\Exception;


class BusinessException extends LaravelBootException
{
    /**
     * 验证是否为业务异常编码
     * @param int $code
     * @return bool
     */
    public static function isValidCode($code)
    {
        return ($code >= 10000 && $code <= 60000) || strlen($code) === 9;
    }
}