<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:43
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface ContextInterface
{
    public function get($key, $default = null);

    public function set($key, $value);

    /**
     * @param static|array $ctx
     * @param bool $override
     * @return mixed
     */
    public function merge($ctx, $override = true);
}