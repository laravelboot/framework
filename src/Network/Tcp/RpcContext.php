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
namespace LaravelBoot\Foundation\Network\Tcp;

use LaravelBoot\Foundation\Utility\Types\Arr;
use LaravelBoot\Foundation\Utility\Types\Json;

class RpcContext
{
    const MAX_NOVA_ATTACH_LEN = 30000; // nova header 总长度 0x7fff;

    const KEY = "rpc-context";

    private $map = [];

    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->map;
        } else {
            return isset($this->map[$key]) ? $this->map[$key] : $default;
        }
    }

    public function set($key, $value)
    {
        $old = null;

        if (isset($this->map[$key])) {
            $old = $this->map[$key];
        }

        if ($value === null) {
            unset($this->map[$key]);
        } else {
            $this->map[$key] = $value;
        }

        return $old;
    }

    public function merge($ctx, $override = true)
    {
        if ($ctx instanceof static) {
            $ctx = $ctx->map;
        }

        if (is_array($ctx) && $ctx) {
            if ($override) {
                $this->map = Arr::merge($this->map, $ctx);
            } else {
                $this->map = Arr::merge($ctx, $this->map);

            }
        }
    }

    /**
     * For Tcp Server
     * @param $novaAttach
     */
    public function unpackNovaAttach($novaAttach)
    {
        if (!strlen($novaAttach)) {
            return;
        }

        try {
            $ctx = Json::decode($novaAttach);
            if (is_array($ctx)) {
                $this->merge($ctx);
            }
        }catch (\Exception $e) {
            sys_error("unpack rpc context fail: $novaAttach");
        }
    }

    /**
     * For Tcp Server & Nova Client
     * @return string
     */
    public function packNovaAttach()
    {
        try {
            $ctx = $this->map;

            if ($ctx === []) {
                $json = "{}"; // for java reason
            } else {
                $json = Json::encode($ctx);
            }
        }catch (\Exception $e) {
            sys_error("pack rpc context fail");
            $json = '{"error": "jsonEncode error"}';
        }

        if (strlen($json) >= self::MAX_NOVA_ATTACH_LEN) {
            $json = '{"error":"len of attach overflow"}';
        }

        return $json;
    }
}