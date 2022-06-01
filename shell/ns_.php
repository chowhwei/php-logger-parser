#!/usr/bin/env php
<?php
/**
 * Usage
 *  ln -s /path/to/ns_.php /etc/munin/plugins/ns_kujiang.com
 */

require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

use Chowhwei\PhpLoggerParser\MuninStatusCode;

(new MuninStatusCode(require_once './config.php'))->handle($argc, $argv);