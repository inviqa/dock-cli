<?php

use Dock\Cli\InstallCommand;
use Dock\Cli\RestartCommand;
use Dock\Cli\SelfUpdateCommand;
use Dock\Cli\UpCommand;
use Dock\Installer\DockerInstaller;
use Dock\Installer\InteractiveProcessRunner;
use Pimple\Container;
use Symfony\Component\Console\Application;

$container = new Container();

$container['command.selfupdate'] = function ($c) {
    return new SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new InstallCommand($c['installer.docker']);
};

$container['runner.process'] = function ($c) {
    return new InteractiveProcessRunner();
};

$container['installer.docker'] = function ($c) {
    return new DockerInstaller($c['runner.process']);
};

$container['command.restart'] = function ($c) {
    return new RestartCommand($c['runner.process']);
};

$container['command.up'] = function ($c) {
    return new UpCommand($c['runner.process']);
};

$container['application'] = function ($c) {
    $application = new Application('Dock CLI', '@package_version@');
    $application->addCommands(
        array(
            $c['command.selfupdate'],
            $c['command.install'],
            $c['command.restart'],
            $c['command.up'],
        )
    );

    return $application;
};

return $container;
