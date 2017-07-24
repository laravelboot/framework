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
namespace LaravelBoot\Foundation\Network\Http\ServerStart;

use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Network\Http\Request\BaseRequest;

class InitializeProxyIps
{
    /**
     * @param \LaravelBoot\Foundation\Network\Http\Server $server
     */
    public function bootstrap($server)
    {
        $proxy = Config::get("server.proxy");
        if (is_array($proxy)) {
            BaseRequest::setTrustedProxies($proxy);
        }
    }
}
