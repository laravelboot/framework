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
namespace LaravelBoot\Foundation\Utility\Types;

class Map
{
    private $data = null;

    public function __construct()
    {
        $this->data = [];
    }

    public function get($key, $default=null)
    {
        if(!isset($this->data[$key])) {
            return $default;
        }

        return $this->data[$key];
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
