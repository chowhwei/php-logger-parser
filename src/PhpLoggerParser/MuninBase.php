<?php

namespace Chowhwei\PhpLoggerParser;

use Chowhwei\MuninPhpPlugin\Graph;

abstract class MuninBase
{
    /** @var Graph $graph */
    protected $graph;
    protected $file_prefix;

    public function __construct(array $config)
    {
        $this->file_prefix = $config['file_prefix'];
    }

    public function handle(int $argc, array $argv)
    {
        $command = $argc > 1 ? $argv[1] : 'run';

        $parts = explode('_', $argv[0]);
        $domain = end($parts);

        $file = $this->file_prefix . $domain;
        if (!file_exists($file)) {
            $data = [];
        } else {
            $data = json_decode(file_get_contents($file), true);
        }

        $this->graph = $this->getGraph($domain, $data);

        switch ($command) {
            case 'config':
                echo $this->config();
                break;
            case 'suggest':
                echo $this->suggest();
                break;
            case 'run':
            default:
                echo $this->run();
                break;
        }
    }

    abstract protected function getGraph(string $domain, array $data): Graph;

    protected function config(): string
    {
        return $this->graph->getConfig();
    }

    protected function suggest():string
    {
        return $this->graph->getGraphTitle();
    }

    protected function run(): string
    {
        return $this->graph->getValues();
    }
}