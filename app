#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__.'/src/database.php';

use Symfony\Component\Console\Application;
use Vcian\PhpDbAuditor\Command\DBStandardCheck;

$application = new Application();

# add our commands
$application->add(new DBStandardCheck());

$application->run();