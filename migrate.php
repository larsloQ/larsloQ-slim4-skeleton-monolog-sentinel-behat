<?php
// application.php

require 'vendor/autoload.php';


// use Helpers\Commands\CreateActionCommand;
// use Helpers\Commands\CreateMiddlewareCommand;
// use Helpers\Commands\CreateModelCommand;
// use Helpers\Commands\CreateScaffoldCommand;
// use Helpers\Commands\MigrationGeneratorCommand;
use Phpmig\Console\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Application;

$application = new Application();
// $application->add(new CreateActionCommand());
// $application->add(new CreateMiddlewareCommand());
// $application->add(new CreateModelCommand());
// $application->add(new CreateScaffoldCommand());
// $application->add(new MigrationGeneratorCommand());
$application->addCommands(array(
            new Command\InitCommand(),
            new Command\StatusCommand(),
            new Command\CheckCommand(),
            new Command\GenerateCommand(),
            new Command\UpCommand(),
            new Command\DownCommand(),
            new Command\MigrateCommand(),
            new Command\RollbackCommand(),
            new Command\RedoCommand()
        ));
$application->run();