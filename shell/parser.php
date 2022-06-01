#!/usr/bin/env php
<?php
require_once '../vendor/autoload.php';

use Chowhwei\PhpLoggerParser\Parser;
use Chowhwei\PhpLoggerParser\Worker;

$config = include_once 'config.php';

$domains = $config['domains'];
$fields = $config['fields'];
$file_prefix = $config['file_prefix'];
$log_path = $config['log_path'];
$parser = new Parser($fields);

$data = [];
(new Worker($log_path, $parser, function ($entry) use ($domains, $file_prefix, &$data) {
    $domain = $entry['host'];
    $status_code = $entry['status'];
    $request_time = floatval($entry['request_time']);
    $response_bytes = intval($entry['response_bytes']);

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

    if (!isset($data[$main_domain]['status'][$status_code])) {
        $data[$main_domain]['status'][$status_code] = 0;
    }
    $data[$main_domain]['status'][$status_code] += 1;

    if (!isset($data[$main_domain]['request_time'])) {
        $data[$main_domain]['request_time'] = [
            's50' => 0,
            's100' => 0,
            's200' => 0,
            's500' => 0,
            's1000' => 0
        ];
    }
    if($request_time < 0.05){
        $data[$main_domain]['request_time']['s50'] += 1;
    }elseif($request_time < 0.1){
        $data[$main_domain]['request_time']['s100'] += 1;
    }elseif($request_time < 0.2){
        $data[$main_domain]['request_time']['s200'] += 1;
    }elseif($request_time < 0.5){
        $data[$main_domain]['request_time']['s500'] += 1;
    }else{
        $data[$main_domain]['request_time']['s1000'] += 1;
    }

    if (!isset($data[$main_domain]['response_bytes'])) {
        $data[$main_domain]['response_bytes'] = 0;
    }
    $data[$main_domain]['response_bytes'] += $response_bytes;

    file_put_contents($fn, json_encode($data[$main_domain], JSON_UNESCAPED_UNICODE));
}))->run();