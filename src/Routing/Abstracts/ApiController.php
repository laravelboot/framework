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
namespace LaravelBoot\Foundation\Routing\Abstracts;

/**
 * Class ApiController.
 */
abstract class ApiController extends Controller
{
    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Send something for handler.
     *
     * @param $handler
     */
    public function send($handler)
    {
    }
}
