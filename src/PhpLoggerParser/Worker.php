<?php

namespace Chowhwei\PhpLoggerParser;

use Exception;

/**
 * 监控日志文件
 */
class Worker
{
    protected $callable;
    protected $log_file;
    protected $parser;

    /**
     * @param $log_file
     * @param Parser $parser
     * @param $callable
     */
    public function __construct($log_file, $parser, $callable)
    {
        $this->log_file = $log_file;
        $this->callable = $callable;
        $this->parser = $parser;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $cmd = "tail -F {$this->log_file}";

        $descriptorspec = [
            0 => ["pipe", "r"],   // stdin is a pipe that the child will read from
            1 => ["pipe", "w"],   // stdout is a pipe that the child will write to
            2 => ["pipe", "w"]    // stderr is a pipe that the child will write to
        ];
        $process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
        try {
            if (is_resource($process)) {
                while ($line = fgets($pipes[1])) {
                    $entry = $this->parser->parse($line);
                    call_user_func($this->callable, $entry);
                }
            }
        } finally {
            if (is_resource($process)) {
                proc_close($process);
            }
        }
    }
}