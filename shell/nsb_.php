#!/usr/bin/env php
<?php
/**
 * Usage
 *  ln -s /path/to/nsb_.php /etc/munin/plugins/nsb_kujiang.com
 */
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

use Chowhwei\PhpLoggerParser\MuninBandwidth;

(new MuninBandwidth(require_once './config.php'))->handle($argc, $argv);