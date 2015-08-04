<?php

use Dock\Dinghy\Boot2DockerCli;
use Dock\Dinghy\SshClient;
use Dock\Doctor;
use Dock\Installer\DNS;
use Dock\Installer\Docker;
use Dock\Installer\System;
use Dock\Installer\TaskProvider;
use Dock\System\Environ\EnvironManipulatorFactory;
use Dock\System\Mac\ShellCreator;
use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;

$container['system.shell_creator'] = function() {
    return new ShellCreator();
};

$container['installer.task_provider'] = function ($c) {
    return new TaskProvider([
        new System\Mac\Homebrew($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\BrewCask($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\PhpSsh($c['console.user_interaction'], $c['process.interactive_runner']),
        new Docker\Dinghy(new Boot2DockerCli($c['process.interactive_runner']), $c['cli.dinghy'], $c['console.user_interaction'], $c['process.interactive_runner']),
        new Dns\Mac\DockerRouting($c['cli.dinghy'], $c['console.user_interaction'], $c['process.interactive_runner']),
        new Dns\Mac\DnsDock(new SshClient(new Session(
            new Configuration(SshClient::DEFAULT_HOSTNAME),
            new Password(SshClient::DEFAULT_USERNAME, SshClient::DEFAULT_PASSWORD)
        )), $c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\Vagrant($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\VirtualBox($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
        new Docker\EnvironmentVariables(new EnvironManipulatorFactory(), $c['console.user_interaction'], $c['process.interactive_runner']),
    ]);
};

$container['doctor.tasks'] = function($c) {
    return array(
        new Doctor\Docker($c['process.interactive_runner']),
        new Doctor\DnsDock($c['process.interactive_runner']),
    );
};
