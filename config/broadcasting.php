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

return [
    'default'     => env('BROADCAST_DRIVER', 'null'),
    'connections' => [
        'pusher' => [
            'driver'  => 'pusher',
            'key'     => env('PUSHER_KEY'),
            'secret'  => env('PUSHER_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),
            'options' => [
            ],
        ],
        'redis'  => [
            'driver'     => 'redis',
            'connection' => 'default',
        ],
        'log'    => [
            'driver' => 'log',
        ],
        'null'   => [
            'driver' => 'null',
        ],
    ],
];
