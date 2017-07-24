<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 13:31
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

use LaravelBoot\Foundation\Application;

interface Bootable
{
    public function bootstrap(Application $app);
}