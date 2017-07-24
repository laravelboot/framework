<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/19 13:37
 * @version
 */
namespace LaravelBoot\Foundation\Network\Contracts;

interface Bootable
{
    public function bootstrap($server);
}