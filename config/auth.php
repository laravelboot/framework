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
    'defaults'  => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],
    'guards'    => [
        'api' => [
            'driver'   => 'passport',
            'provider' => 'users',
        ],
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => '',
        ],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_resets',
            'expire'   => 60,
        ],
    ],
];
