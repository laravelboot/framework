<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/19 15:55
 * @version
 */
return [
    'host' => '0.0.0.0',
    'port' =>9501,
    'config' => [
        'worker_num'=>4,
        'daemonize'=>1
    ],
    'monitor' => [
        'max_request'=>100000,
        'max_live_time'=>1800000,
        'check_interval'=>10000,
        'memory_limit'=>1.5*1024*1024*1024,
        'cpu_limit'=>12,
        'debug'=>false
    ],
    'request_timeout' => 30*1000,
    'paths'=>[
        'routing'=>__DIR__
    ]
];