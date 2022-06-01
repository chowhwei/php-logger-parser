#!/usr/bin/env php
<?php
/**
 * Usage
 *  ln -s /path/to/nsr_.php /etc/munin/plugins/nsr_kujiang.com
 */
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

use Chowhwei\PhpLoggerParser\MuninRequestTime;

(new MuninRequestTime(require_once './config.php'))->handle($argc, $argv);