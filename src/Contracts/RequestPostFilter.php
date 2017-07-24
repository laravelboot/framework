<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:38
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface RequestPostFilter
{
    public function postFilter(Request $request,$response,Context $context);
}