<?php

use Dock\Cli\InstallCommand;
use Dock\Cli\RestartCommand;
use Dock\Cli\SelfUpdateCommand;
use Dock\Cli\UpCommand;
use Dock\Installer\DockerInstaller;
use Dock\Cli\IO\InteractiveProcessRunner;
use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\IO\SilentProcessRunner;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

$container = new Container();

$container['command.selfupdate'] = function ($c) {
    return new SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new InstallCommand($c['installer.docker']);
};

$container['console.user_interaction'] = function($c) {
    $userInteraction = new ConsoleUserInteraction();

    $c['event_dispatcher']->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) use ($userInteraction) {
        $userInteraction->onCommand($event);
    });

    return $userInteraction;
};

$container['process.interactive_runner'] = function ($c) {
    return new InteractiveProcessRunner($c['console.user_interaction']);
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
$container['event_dispatcher'] = function() {
    return new EventDispatcher();
};

$container['application'] = function ($c) {
    $application = new Application('Dock CLI', '@package_version@');
    $application->setDispatcher($c['event_dispatcher']);
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
