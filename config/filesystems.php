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
    'default' => 'local',
    'cloud'   => 's3',
    'disks'   => [
        'local'  => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],
        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'visibility' => 'public',
        ],
        's3'     => [
            'driver' => 's3',
            'key'    => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],
    ],
];
