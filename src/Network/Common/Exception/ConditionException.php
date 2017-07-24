<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/20 14:38
 * @version
 */
namespace LaravelBoot\Foundation\Network\Common\Exception;

use Psr\Log\LogLevel;
use LaravelBoot\Foundation\Exception\LaravelBootException;

class ConditionException extends LaravelBootException
{
    public $logLevel = LogLevel::ERROR;
}