<?php

use Dock\Cli\DoctorCommand;
use Dock\Cli\Helper\Project;
use Dock\Cli\InstallCommand;
use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Cli\IO\InteractiveProcessRunner;
use Dock\Cli\LogsCommand;
use Dock\Cli\PsCommand;
use Dock\Cli\RestartCommand;
use Dock\Cli\RunCommand;
use Dock\Cli\SelfUpdateCommand;
use Dock\Cli\StartCommand;
use Dock\Cli\StopCommand;
use Dock\Compose\ComposeExecutableFinder;
use Dock\Compose\Config;
use Dock\Containers\ConfiguredContainers;
use Dock\Dinghy\DinghyCli;
use Dock\Docker\ContainerDetails;
use Dock\Docker\Dns\DnsDockResolver;
use Dock\DockerCompose\ConfiguredContainerIds;
use Dock\DockerCompose\Logs;
use Dock\Doctor\Doctor;
use Dock\Installer\DockerInstaller;
use Dock\IO\Process\InteractiveProcessBuilder;
use Dock\IO\SilentProcessRunner;
use Dock\System\OperatingSystemDetector;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

$container = new Container();

$osDetector = new OperatingSystemDetector();
if ($osDetector->isMac()) {
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.mac.php';
} elseif ($osDetector->isDebian()) {
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.debian.php';
} elseif ($osDetector->isRedHat()) {
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.redhat.php';
} else {
    throw new \Exception($osDetector->isLinux()
        ? "Installer does not support linux distribution: " . $osDetector->getLinuxDistribution()
        : "Installer does not support operating system: " . $osDetector->getOperatingSystem());
}

$container['command.selfupdate'] = function () {
    return new SelfUpdateCommand();
};

$container['command.install'] = function ($c) {
    return new InstallCommand($c['installer.docker'], $c['system.shell_creator']);
};

$container['doctor'] = function ($c) {
    return new Doctor($c['doctor.tasks']);
};

$container['command.doctor'] = function ($c) {
    return new DoctorCommand($c['doctor']);
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
    return new StartCommand(new InteractiveProcessBuilder($c['console.user_interaction']), $c['console.user_interaction']);
};
$container['command.stop'] = function ($c) {
    return new StopCommand($c['compose.executable_finder'], $c['console.user_interaction'], $c['process.silent_runner']);
};
$container['command.ps'] = function ($c) {
    return new PsCommand(new ConfiguredContainers(
        $c['containers.configured_container_ids'],
        $c['containers.container_details']
    ));
};
$container['containers.configured_container_ids'] = function ($c) {
    return new ConfiguredContainerIds($c['process.silent_runner']);
};
$container['containers.container_details'] = function ($c) {
    return new ContainerDetails($c['process.silent_runner'], new DnsDockResolver());
};

$container['command.logs'] = function ($c) {
    return new LogsCommand($c['logs']);
};

$container['cli.helper.project'] = function ($c) {
    return new Project();
};

$container['compose.config'] = function ($c) {
    return new Config($c['cli.helper.project']);
};

$container['command.run'] = function ($c) {
    return new RunCommand($c['process.interactive_runner'], $c['compose.config']);
};

$container['event_dispatcher'] = function () {
    return new EventDispatcher();
};

$container['cli.dinghy'] = function ($c) {
    return new DinghyCli($c['process.interactive_runner']);
};

$container['logs'] = function ($c) {
    return new Logs($c['compose.executable_finder'], $c['process.silent_runner']);
};

$container['application'] = function ($c) {
    $application = new Application('Dock CLI', '@package_version@');
    $application->setDispatcher($c['event_dispatcher']);
    $application->addCommands([
        $c['command.selfupdate'],
        $c['command.doctor'],
        $c['command.install'],
        $c['command.restart'],
        $c['command.run'],
        $c['command.start'],
        $c['command.stop'],
        $c['command.ps'],
        $c['command.logs'],
    ]);

    return $application;
};

return $container;
