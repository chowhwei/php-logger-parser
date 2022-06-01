<?php

namespace Chowhwei\PhpLoggerParser;

use Chowhwei\MuninPhpPlugin\Field;
use Chowhwei\MuninPhpPlugin\Graph;

class MuninBandwidth extends MuninBase
{
    protected function getGraph(string $domain, array $data): Graph
    {
        $graph = (new Graph('带宽监控 - ' . $domain, 'nginx'))
            ->setGraphVlabel('count')
            ->setGraphArgs('--base 1000 --logarithmic')
            ->setGraphInfo('统计' . $domain . '带宽');

        $data = $data['response_bytes'] ?? [];

        $name = 'response-bytes';
        $field = (new Field($name))
            ->setLabel('response bytes')
            ->setDraw(Graph::FIELD_DRAW_LINE1)
            ->setType(Graph::FIELD_TYPE_DERIVE)
            ->setMin(0)
            ->setValue($data);
        $graph->appendField($field);

        return $graph;
    }
}