#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__.'/src/database.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Vcian\PhpDbAuditor\Command\DBStandardCheck;
use Vcian\PhpDbAuditor\Command\DBConstraintCheck;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vcian\PhpDbAuditor\Constants\Constant;

$application = new Application();

$input = new ArgvInput();
$output = new ConsoleOutput();

$io = new SymfonyStyle($input, $output);

$commandSelect = $io->choice('Please Select feature which would you like to do',[Constant::STANDARD_COMMAND, Constant::CONSTRAINT_COMMAND]);

if ($commandSelect === Constant::STANDARD_COMMAND) {
    $application->add(new DBStandardCheck());
}

if ($commandSelect === Constant::CONSTRAINT_COMMAND) {
    $application->add(new DBConstraintCheck());
}

$application->run();