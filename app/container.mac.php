<?php

use Dock\Docker\Machine\SshClient;
use Dock\Installer\DNS;
use Dock\Installer\System;
use Dock\Installer\TaskProvider;
use Dock\IO\PharFileExtractor;
use Dock\Project\Decorator\ProjectShouldBeInHomeDirectory;
use Dock\System\Environ\EnvironManipulatorFactory;

$container['installer.dns.dnsdock'] = function($c) {
    return new DNS\Mac\DnsDock(new SshClient($c['machine']), $c['console.user_interaction'], $c['process.interactive_runner'], $c['machine']);
};
$container['installer.dns.download_dnsdock'] = function($c) {
    return new DNS\Mac\DownloadDnsDock($c['console.user_interaction'], $c['process.interactive_runner']);
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
        new System\Mac\DockerMachine($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\Docker($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\Machine($c['console.user_interaction'], $c['machine']),
        $c['installer.dns.download_dnsdock'],
        $c['installer.dns.dnsdock'],
        $c['installer.dns.docker_routing'],
        new System\Mac\Vagrant($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\VirtualBox($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Mac\EnvironmentVariables($c['console.user_interaction'], new EnvironManipulatorFactory(), $c['machine']),
    ]);
};

$container->extend('project.manager', function ($projectManager, $c) {
    return new ProjectShouldBeInHomeDirectory($projectManager, $c['console.user_interaction']);
});
