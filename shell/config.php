<?php

return [
    'domains' => [
        'kujiang.com'
    ],

    'fields' => [
        '{remote_ip}',
        '{time}',
        '{upstream_cache_status}',
        '"{request}"',  //'"{request_method} {request} {protocol}"',
        '{status}',
        '{response_bytes}',
        '{request_time}',
        '"{referer}"',
        '{host}',
        '"{user_agent}"',
        '"{http_x_forwarded_for}"',
        '{gzip_ratio}'
    ],

    'file_prefix' => './ns-',
//    'file_prefix' => '/dev/shm/ns-',

    'log_path' => './access.log',
//    'log_path' => '/var/log/nginx/access.log'

    'error' => './error.log'
];
