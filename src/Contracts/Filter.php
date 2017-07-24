<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:02
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface Filter
{
    public function doFilter($request,$response,$context);
}
