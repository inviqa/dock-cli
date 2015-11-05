<?php

use Dock\Dinghy\Boot2DockerCli;
use Dock\Dinghy\SshClient;
use Dock\Doctor;
use Dock\Installer\DNS;
use Dock\Installer\Docker;
use Dock\Installer\System;
use Dock\Installer\TaskProvider;
use Dock\IO\PharFileExtractor;
use Dock\System\Environ\EnvironManipulatorFactory;
use Dock\System\Mac\ShellCreator;
use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;

$container['system.shell_creator'] = function() {
    return new ShellCreator();
};

$container['installer.dns.dnsdock'] = function($c) {
    return new DNS\Mac\DnsDock(new SshClient($c['cli.dinghy']), $c['console.user_interaction'], $c['process.interactive_runner']);
};
$container['installer.dns.docker_routing'] = function($c) {
    return new DNS\Mac\DockerRouting($c['machine'], $c['console.user_interaction'], $c['process.interactive_runner'], $c['io.phar_file_extractor']);
};

$container['io.phar_file_extractor'] = function() {
    return new PharFileExtractor();
};

$container['installer.task_provider'] = function ($c) {
    return new TaskProvider([
        new System\Mac\Homebrew($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\BrewCask($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\PhpSsh($c['console.user_interaction'], $c['process.interactive_runner']),
        new Docker\Dinghy(new Boot2DockerCli($c['process.interactive_runner'], $c['io.phar_file_extractor']), $c['cli.dinghy'], $c['console.user_interaction'], $c['process.interactive_runner']),
        $c['installer.dns.dnsdock'],
        $c['installer.dns.docker_routing'],
        new System\Mac\Vagrant($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\VirtualBox($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
        new Docker\EnvironmentVariables(new EnvironManipulatorFactory(), $c['console.user_interaction'], $c['process.interactive_runner'], $c['machine']),
    ]);
};

$container['doctor.tasks'] = function($c) {
    return [
        new Doctor\Docker($c['process.silent_runner'], $c['installer.docker']),
        new Doctor\DnsDock($c['process.silent_runner'], $c['installer.dns.dnsdock'], $c['installer.dns.docker_routing']),
    ];
};
