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
    'default' => env('CACHE_DRIVER', 'file'),
    'stores'  => [
        'apc'       => [
            'driver' => 'apc',
        ],
        'array'     => [
            'driver' => 'array',
        ],
        'database'  => [
            'driver'     => 'database',
            'table'      => 'caches',
            'connection' => null,
        ],
        'file'      => [
            'driver' => 'file',
            'path'   => storage_path('caches'),
        ],
        'memcached' => [
            'driver'        => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl'          => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options'       => [
            ],
            'servers'       => [
                [
                    'host'   => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port'   => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],
        'redis'     => [
            'driver'     => 'redis',
            'connection' => 'default',
        ],
    ],
    'prefix'  => 'notadd',
];
