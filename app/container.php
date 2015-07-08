<?php

use Dock\Cli\InstallCommand;
use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Cli\IO\InteractiveProcessRunner;
use Dock\Cli\LogsCommand;
use Dock\Cli\PsCommand;
use Dock\Cli\RestartCommand;
use Dock\Cli\SelfUpdateCommand;
use Dock\Cli\StartCommand;
use Dock\Cli\StopCommand;
use Dock\Compose\ComposeExecutableFinder;
use Dock\Compose\Inspector;
use Dock\Dinghy\Boot2DockerCli;
use Dock\Dinghy\DinghyCli;
use Dock\Dinghy\SshClient;
use Dock\Installer\DNS\DnsDock;
use Dock\Installer\DNS\DockerRouting;
use Dock\Installer\Docker\Dinghy;
use Dock\Installer\Docker\EnvironmentVariables;
use Dock\Installer\DockerInstaller;
use Dock\Installer\System\BrewCask;
use Dock\Installer\System\DockerCompose;
use Dock\Installer\System\Homebrew;
use Dock\Installer\System\PhpSsh;
use Dock\Installer\System\Vagrant;
use Dock\Installer\System\VirtualBox;
use Dock\IO\SilentProcessRunner;
use Dock\System\Environ\EnvironManipulatorFactory;
use Pimple\Container;
use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;
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
$container['compose.executable_finder'] = function () {
    return new ComposeExecutableFinder();
};
$container['installer.docker'] = function ($c) {
    return new DockerInstaller(
        new \SRIO\ChainOfResponsibility\ChainBuilder([
            new Homebrew($c['console.user_interaction'], $c['process.interactive_runner']),
            new BrewCask($c['console.user_interaction'], $c['process.interactive_runner']),
            new PhpSsh($c['console.user_interaction'], $c['process.interactive_runner']),
            new Dinghy(new Boot2DockerCli($c['process.interactive_runner']), $c['cli.dinghy'], $c['console.user_interaction'], $c['process.interactive_runner']),
            new DockerRouting($c['cli.dinghy'], $c['console.user_interaction'], $c['process.interactive_runner']),
            new DnsDock(new SshClient(new Session(
                new Configuration(SshClient::DEFAULT_HOSTNAME),
                new Password(SshClient::DEFAULT_USERNAME, SshClient::DEFAULT_PASSWORD)
            )), $c['console.user_interaction'], $c['process.interactive_runner']),
            new Vagrant($c['console.user_interaction'], $c['process.interactive_runner']),
            new VirtualBox($c['console.user_interaction'], $c['process.interactive_runner']),
            new DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
            new EnvironmentVariables(new EnvironManipulatorFactory(), $c['console.user_interaction'], $c['process.interactive_runner']),
        ])
    );
};

$container['command.restart'] = function ($c) {
    return new RestartCommand(new DinghyCli($c['process.interactive_runner']));
};

$container['command.start'] = function ($c) {
    return new StartCommand($c['process.silent_runner'], $c['console.user_interaction']);
};
$container['command.stop'] = function ($c) {
    return new StopCommand($c['compose.executable_finder'], $c['console.user_interaction']);
};
$container['command.ps'] = function ($c) {
    return new PsCommand(new Inspector($c['process.silent_runner']));
};
$container['command.logs'] = function ($c) {
    return new LogsCommand($c['compose.executable_finder']);
};
$container['event_dispatcher'] = function () {
    return new EventDispatcher();
};

$container['cli.dinghy'] = function ($c) {
    return new DinghyCli($c['process.interactive_runner']);
};

$container['application'] = function ($c) {
    $application = new Application('Dock CLI', '@package_version@');
    $application->setDispatcher($c['event_dispatcher']);
    $application->addCommands(
        array(
            $c['command.selfupdate'],
            $c['command.install'],
            $c['command.restart'],
            $c['command.start'],
            $c['command.stop'],
            $c['command.ps'],
            $c['command.logs'],
        )
    );

    return $application;
};

return $container;
