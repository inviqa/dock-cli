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
use Dock\Installer\DNS;
use Dock\Installer\Docker;
use Dock\Installer\DockerInstaller;
use Dock\Installer\System;
use Dock\Installer\TaskProvider;
use Dock\Installer\TaskProviderFactory;
use Dock\IO\SilentProcessRunner;
use Dock\System\Environ\EnvironManipulatorFactory;
use Dock\System\OperatingSystemDetector;
use Pimple\Container;
use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

$container = new Container();

$osDetector = new OperatingSystemDetector();
$operatingSystem = $osDetector->get();

if ($operatingSystem === OperatingSystemDetector::MAC) {
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.mac.php';
} elseif ($operatingSystem === OperatingSystemDetector::LINUX) {
    $distro = $osDetector->getLinuxDistro();
    switch ($distro) {
        case 'debian':
        case 'ubuntu':
        case 'linuxmint':
        case 'elementary os':
        case 'kali':
            require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.debian.php';
            break;
        case 'redhat':
        case 'amzn':
        case 'fedora':
        case 'centos':
            // require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.redhat.php';
            // break;
        default:
            throw new \Exception("Linux distribution '$distro' is not supported.");
    }
} else {
    throw new \Exception("Installer does not support operating system '$operatingSystem'");
}

$container['command.selfupdate'] = function () {
    return new SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new InstallCommand($c['installer.docker'], $c['system.shell_creator']);
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
    return new DockerInstaller($c['installer.task_provider']);
};

$container['command.restart'] = function ($c) {
    return new RestartCommand(new DinghyCli($c['process.interactive_runner']));
};

$container['command.start'] = function ($c) {
    return new StartCommand($c['process.silent_runner'], $c['console.user_interaction']);
};
$container['command.stop'] = function ($c) {
    return new StopCommand($c['compose.executable_finder'], $c['console.user_interaction'], $c['process.silent_runner']);
};
$container['command.ps'] = function ($c) {
    return new PsCommand(new Inspector($c['process.silent_runner']));
};
$container['command.logs'] = function ($c) {
    return new LogsCommand($c['compose.executable_finder'], $c['process.silent_runner']);
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
