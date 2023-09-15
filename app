#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__.'/src/database.php';

use Symfony\Component\Console\Application;
use Vcian\PhpDbAuditor\Command\DBStandardCheck;
use Vcian\PhpDbAuditor\Command\DBConstraintCheck;

$application = new Application();

$application->add(new DBStandardCheck());
$application->add(new DBConstraintCheck());

$application->run();