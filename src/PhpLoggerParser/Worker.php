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
    protected $error;
    protected $parser;

    /**
     * @param array $config
     * @param Parser $parser
     * @param $callable
     */
    public function __construct(array $config, $parser, $callable)
    {
        $this->log_file = $config['log_path'];
        $this->error = $config['error'];
        $this->callable = $callable;
        $this->parser = $parser;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $cmd = "tail -F {$this->log_file}";

        $descriptor_spec = [
            0 => ["pipe", "r"],   // stdin is a pipe that the child will read from
            1 => ["pipe", "w"],   // stdout is a pipe that the child will write to
            2 => ["pipe", "w"]    // stderr is a pipe that the child will write to
        ];

        if(!$fp = fopen($this->error, 'a')){
            echo "Cannot open file $this->error";
            exit;
        }

        $process = proc_open($cmd, $descriptor_spec, $pipes);
        if (is_resource($process)) {
            fclose($pipes[0]);
            while ($line = fgets($pipes[1])) {
                try {
                    $entry = $this->parser->parse($line);
                    call_user_func($this->callable, $entry);
                }catch (Exception $ex){
                    fwrite($fp, $line);
                }
            }
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }

        fclose($fp);
    }
}