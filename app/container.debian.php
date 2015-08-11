<?php

use Dock\Doctor;
use Dock\Installer\DNS;
use Dock\Installer\Docker;
use Dock\Installer\System;
use Dock\Installer\TaskProvider;
use Dock\System\Linux\ShellCreator;

$container['system.shell_creator'] = function() {
    return new ShellCreator();
};

$container['installer.dns.dnsdock'] = function($c) {
    return new Dns\Linux\DnsDock($c['console.user_interaction'], $c['process.interactive_runner']);
};
$container['installer.dns.docker_routing'] = function($c) {
    return new Dns\Linux\Debian\DockerRouting($c['console.user_interaction'], $c['process.interactive_runner']);
};

$container['installer.task_provider'] = function ($c) {
    return new TaskProvider([
        new System\Linux\Debian\NoSudo($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Linux\Docker($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Linux\DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
        $c['installer.dns.dnsdock'],
        $c['installer.dns.docker_routing'],
    ]);
};

$container['doctor.tasks'] = function($c) {
    return [
        new Doctor\Docker($c['process.silent_runner'], $c['installer.docker']),
        new Doctor\DnsDock($c['process.silent_runner'], $c['installer.dns.dnsdock'], $c['installer.dns.docker_routing']),
    ];
};
