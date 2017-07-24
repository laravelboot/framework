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

interface RequestTerminator
{
    public function terminate(Request $request,$response,Context $context);
}
