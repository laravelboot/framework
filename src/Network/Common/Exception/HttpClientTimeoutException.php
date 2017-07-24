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
namespace LaravelBoot\Foundation\Network\Common\Exception;

use Exception;
use LaravelBoot\Foundation\Exception\LaravelBootException;

class HttpClientTimeoutException extends LaravelBootException
{
    public function __construct($message = '', $code = 408, Exception $previous = null, array $metaData = [])
    {
        parent::__construct($message, $code, $previous);
    }
}