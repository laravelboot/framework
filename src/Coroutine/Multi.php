<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 15:01
 * @version
 */
namespace LaravelBoot\Foundation\Coroutine;

class Multi
{
    private $result = [];
    private $request = [];
    private $response = [];

    public function __construct()
    {
        $this->init();
    }

    public static function newInstance()
    {
        $instance = new self();

        return $instance;
    }

    public function init()
    {
        $this->result = [];
        $this->request = [];
        $this->response = [];
    }

    public function add($key, callable $callback)
    {


        return $this;
    }

    public function execute()
    {
        return $this->result;
    }
}
