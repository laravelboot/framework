<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:21
 * @version
 */
namespace LaravelBoot\Foundation\Foundation\Coroutine;

use LaravelBoot\Foundation\Utility\Types\Dir;

class Commands
{
    public static function load()
    {
        $dir = __DIR__ . '/Command/';
        $files = Dir::glob($dir, '*.php');

        if (!$files) return false;

        foreach($files as $file){
            require_once($file);
        }
    }
}






