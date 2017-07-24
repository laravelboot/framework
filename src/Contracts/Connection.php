<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 13:56
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface Connection
{
    public function getSocket();

    public function release();

    public function close();

    public function getEngine();

    public function getConfig();

    public function heartbeat();
}