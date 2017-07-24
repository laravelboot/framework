<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/20 19:19
 * @version
 */
return [
    //是否运行, pre不需要运行
    'run' => false,
    'host' => '1.1.1.1',
    'port' => 1234,
    'uri' => '/monitor/push',
    //多久上报一次，单位毫秒
    'time' => 60000,
];