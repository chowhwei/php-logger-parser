#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Chowhwei\PhpLoggerParser\Command\TailCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new TailCommand());

$application->run();