<?php

use Dock\Cli\InstallCommand;
use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Cli\IO\InteractiveProcessRunner;
use Dock\Cli\LogsCommand;
use Dock\Cli\PsCommand;
use Dock\Cli\RestartCommand;
use Dock\Cli\SelfUpdateCommand;
use Dock\Cli\UpCommand;
use Dock\Compose\Inspector;
use Dock\Dinghy\Boot2DockerCli;
use Dock\Dinghy\DinghyCli;
use Dock\Dinghy\SshClient;
use Dock\Installer\DNS\DnsDock;
use Dock\Installer\DNS\DockerRouting;
use Dock\Installer\Docker\Dinghy;
use Dock\Installer\Docker\EnvironmentVariables;
use Dock\Installer\DockerInstaller;
use Dock\Installer\InstallContext;
use Dock\Installer\System\BrewCask;
use Dock\Installer\System\DockerCompose;
use Dock\Installer\System\Homebrew;
use Dock\Installer\System\PhpSsh;
use Dock\Installer\System\Vagrant;
use Dock\Installer\System\VirtualBox;
use Dock\IO\SilentProcessRunner;
use Dock\System\Environ\EnvironManipulatorFactory;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

$container = new Container();

$container['command.selfupdate'] = function () {
    return new SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new InstallCommand($c['installer.docker']);
};

$container['console.user_interaction'] = function ($c) {
    $userInteraction = new ConsoleUserInteraction();

    $c['event_dispatcher']->addListener(
        ConsoleEvents::COMMAND,
        function (ConsoleCommandEvent $event) use ($userInteraction) {
            $userInteraction->onCommand($event);
        }
    );

    return $userInteraction;
};

$container['process.interactive_runner'] = function ($c) {
    return new InteractiveProcessRunner($c['console.user_interaction']);
};

$container['process.silent_runner'] = function () {
    return new SilentProcessRunner();
};

$container['installer.docker'] = function ($c) {
    $dinghyCli = new DinghyCli($c['process.interactive_runner']);
    return new DockerInstaller(
        new InstallContext($c['process.interactive_runner'], $c['console.user_interaction']),
        new \SRIO\ChainOfResponsibility\ChainBuilder([
            new Homebrew(),
            new BrewCask(),
            new PhpSsh(),
            new Dinghy(new Boot2DockerCli($c['process.interactive_runner']), $dinghyCli),
            new DockerRouting($dinghyCli),
            new DnsDock(new SshClient()),
            new Vagrant(),
            new VirtualBox(),
            new DockerCompose(),
            new EnvironmentVariables(new EnvironManipulatorFactory()),
        ])
    );
};

$container['command.restart'] = function ($c) {
    return new RestartCommand(new DinghyCli($c['process.interactive_runner']));
};

$container['command.up'] = function ($c) {
    return new UpCommand($c['process.silent_runner'], $c['console.user_interaction']);
};
$container['command.ps'] = function ($c) {
    return new PsCommand(new Inspector($c['process.silent_runner']));
};
$container['command.logs'] = function ($c) {
    return new LogsCommand($c['process.interactive_runner']);
};
$container['event_dispatcher'] = function () {
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
            $c['command.ps'],
            $c['command.logs'],
        )
    );

    return $application;
};

return $container;
