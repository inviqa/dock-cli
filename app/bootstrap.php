<?php

use Dock\Cli\InstallCommand;
use Dock\Cli\RestartCommand;
use Dock\Cli\SelfUpdateCommand;
use Dock\Cli\UpCommand;
use Dock\Installer\DockerInstaller;
use Dock\Installer\InteractiveProcessRunner;
use Dock\IO\SilentProcessRunner;
use Pimple\Container;
use Symfony\Component\Console\Application;

$container = new Container();

$container['command.selfupdate'] = function ($c) {
    return new SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new InstallCommand($c['installer.docker']);
};

$container['process.interactive_runner'] = function () {
    return new InteractiveProcessRunner();
};
$container['process.silent_runner'] = function () {
    return new SilentProcessRunner();
};

$container['installer.docker'] = function ($c) {
    return new DockerInstaller($c['process.interactive_runner']);
};

$container['command.restart'] = function ($c) {
    return new RestartCommand($c['process.interactive_runner']);
};

$container['command.up'] = function ($c) {
    return new UpCommand($c['process.silent_runner']);
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
