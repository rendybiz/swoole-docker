<?php
use Mezzio\Swoole\ConfigProvider;

return array_merge((new ConfigProvider())(), [
    'mezzio-swoole' => [
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => 9501, // use an integer value here
        ],
    ],
]);