<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/21 23:40
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

use Exception;

interface ExceptionHandler
{
    public function handle(Exception $e);
}