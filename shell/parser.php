#!/usr/bin/env php
<?php
require_once '../vendor/autoload.php';

use Chowhwei\PhpLoggerParser\Parser;
use Chowhwei\PhpLoggerParser\Worker;

$domains = [
    'abl78.com',
    'bailuxiaoshuo.com',
    'kjcdn.com',
    'kjmgls.com',
    'kqingyun.com',
    'kujiang.com',
    'kujiang.net',
    'mhuyr.com',
    're5665.com',
    'reluac.com',
    'rl8899.com',
    'rlcps.cn',
    'taolewx.com',
    'wan123x.com',
    'weryy.com',
    'wlm818.com',
    'wlmcps.com',
    'wlmxiaoshuo.com'
];

$fields = [
    '{remote_ip}',
    '{time}',
    '{upstream_cache_status}',
    '"{request_method} {request} {protocol}"',
    '{status}',
    '{response_bytes}',
    '{request_time}',
    '"{referer}"',
    '{host}',
    '"{user_agent}"',
    '"{http_x_forwarded_for}"',
    '{gzip_ratio}'
];
$parser = new Parser($fields);

//$file_prefix = '/dev/shm/ns-';
$file_prefix = './ns-';

$data = [];
(new Worker('./access.log', $parser, function ($entry) use ($domains, $file_prefix, &$data) {
    $domain = $entry['host'];
    $status_code = $entry['status'];

    $parts = explode('.', $domain);
    $parts = array_slice($parts, -2);
    $main_domain = implode('.', $parts);

    if (!in_array($main_domain, $domains)) {
        return;
    }

    if ($domain == '-') {
        return;
    }

    if ($status_code == '') {
        return;
    }

    $fn = $file_prefix . $main_domain;
    if (!isset($data[$main_domain])) {
        if (file_exists($fn)) {
            $data[$main_domain] = json_decode(file_get_contents($fn), true);
        } else {
            $data[$main_domain] = [];
        }
    }

    if (!isset($data[$main_domain][$status_code])) {
        $data[$main_domain][$status_code] = 0;
    }
    $data[$main_domain][$status_code] = $data[$main_domain][$status_code] + 1;
    file_put_contents($fn, json_encode($data[$main_domain], JSON_UNESCAPED_UNICODE));
}))->run();