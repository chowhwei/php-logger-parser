#!/usr/bin/env php
<?php
/**
 * Usage
 *  php phpLogTailer.phar
 */

if(file_exists('./phpLogTailer.phar')) {
    @unlink('./phpLogTailer.phar');
}
$phar = new Phar('phpLogTailer.phar.tar');
$phar->buildFromDirectory('../');

$phar = $phar->convertToExecutable(Phar::PHAR, Phar::BZ2, '.phar');
$phar->setStub($phar->createDefaultStub('../shell/parser.php'));
if(file_exists('./phpLogTailer.phar.tar')) {
    @unlink('./phpLogTailer.phar.tar');
}