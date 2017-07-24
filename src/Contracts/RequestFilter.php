<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:01
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface RequestFilter
{
    public function doFilter(Request $request,Context $context);
}