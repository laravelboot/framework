<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 19:37
 * @version
 */
namespace LaravelBoot\Foundation\ServiceManager;

interface ServiceRegistry
{
    public function register($config);

    public function refreshing($config);

    public function watch();

    public function lookup();
}