<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Network\Server\WorkerStart;

use ErrorException;
use LaravelBoot\Foundation\Network\Contracts\Bootable;

class InitializeErrorHandler implements Bootable
{

    public function bootstrap($server)
    {
        $debug = true;
        if($debug){
            set_error_handler([self::class,'handleError'],E_ALL & ~E_DEPRECATED);
        } else{
            set_error_handler([self::class,'handleError'],E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        }
    }

    public static function handleError($code,$message,$file,$line)
    {
        $context = "catched an error! errno: $code, message: $message, file: $file:$line";
        sys_echo($context);
        throw new ErrorException($context,$code);
    }
}
