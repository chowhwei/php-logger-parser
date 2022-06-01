#!/usr/bin/env php
<?php

require_once '../vendor/autoload.php';

use Chowhwei\PhpLoggerParser\Collector;

(new Collector())->collect(include_once 'config.php');