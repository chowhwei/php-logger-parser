#!/usr/bin/env php
<?php
/**
 * Usage
 *  ln -s /path/to/ns_.php /etc/munin/plugins/ns_kujiang.com
 */

use Chowhwei\MuninPhpPlugin\Field;
use Chowhwei\MuninPhpPlugin\Graph;

require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

$command = $argc > 1 ? $argv[1] : 'run';

$parts = explode('_', $argv[0]);
$domain = end($parts);

$graph = (new Graph('状态码监控 - ' . $domain, 'nginx'))
    ->setGraphVlabel('count')
    ->setGraphArgs('--base 1000 --logarithmic')
    ->setGraphInfo('统计' . $domain . '下所有状态码数量');

$file = '/dev/shm/ns-' . $domain;
if (!file_exists($file)) {
    $data = [];
} else {
    $data = json_decode(file_get_contents($file), true);
}

if (count($data) > 0) {
    ksort($data);
    foreach ($data as $status_code => $count) {
        $name = 'http-' . $status_code;
        $field = (new Field($name))
            ->setLabel($status_code)
            ->setDraw(Graph::FIELD_DRAW_LINE1)
            ->setType(Graph::FIELD_TYPE_DERIVE)
            ->setMin(0)
            ->setValue($count);
        $graph->appendField($field);
    }
}

switch ($command) {
    case 'config':
        echo $graph->getConfig();
        break;
    case 'suggest':
        echo $graph->getGraphTitle();
        break;
    case 'run':
    default:
        echo $graph->getValues();
        break;
}