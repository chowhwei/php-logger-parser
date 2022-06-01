<?php

namespace Chowhwei\PhpLoggerParser;

use Chowhwei\MuninPhpPlugin\Field;
use Chowhwei\MuninPhpPlugin\Graph;

class MuninStatusCode extends MuninBase
{
    protected function getGraph(string $domain, array $data): Graph
    {
        $graph = (new Graph('状态码监控 - ' . $domain, 'nginx'))
            ->setGraphVlabel('count')
            ->setGraphArgs('--base 1000 --logarithmic')
            ->setGraphInfo('统计' . $domain . '下所有状态码数量');

        $data = $data['status'] ?? [];

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

        return $graph;
    }
}