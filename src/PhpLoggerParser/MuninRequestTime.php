<?php

namespace Chowhwei\PhpLoggerParser;

use Chowhwei\MuninPhpPlugin\Field;
use Chowhwei\MuninPhpPlugin\Graph;

class MuninRequestTime extends MuninBase
{
    protected function getGraph(string $domain, array $data): Graph
    {
        $graph = (new Graph('请求时间监控 - ' . $domain, 'nginx'))
            ->setGraphVlabel('count')
            ->setGraphArgs('--base 1000 --logarithmic')
            ->setGraphInfo('统计' . $domain . '下请求时间');

        $data = $data['request_time'] ?? [];

        if (count($data) > 0) {
            ksort($data);
            foreach ($data as $second => $count) {
                $name = 'time-' . str_replace('.', '_', $second);
                $field = (new Field($name))
                    ->setLabel($second)
                    ->setDraw(Graph::FIELD_DRAW_LINE1)
                    ->setType(Graph::FIELD_TYPE_DERIVE)
                    ->setMin(0)
                    ->setValue($count);
                $graph->appendField($field);
            }
        }

        return $graph;
    }
}