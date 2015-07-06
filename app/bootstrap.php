<?php

use Pimple\Container;
use Symfony\Component\Console\Application;

$container = new Container();

$container['command.help'] = function ($c) {
    return new \Symfony\Component\Console\Command\HelpCommand();
};

$container['command.list'] = function ($c) {
    return new \Symfony\Component\Console\Command\ListCommand();
};

$container['command.selfupdate'] = function ($c) {
    return new \Dock\Cli\SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new \Dock\Cli\InstallCommand();
};

$container['command.restart'] = function ($c) {
    return new \Dock\Cli\RestartCommand();
};

$container['command.up'] = function ($c) {
    return new \Dock\Cli\UpCommand();
};


$container['application'] = function ($c) {
    $application = new Application('Dock CLI', '@package_version@');
    $application->addCommands(
        array(
            $c['command.help'],
            $c['command.list'],
            $c['command.selfupdate'],
            $c['command.install'],
            $c['command.restart'],
            $c['command.up']
        )
    );

    return $application;
};

return $container;
