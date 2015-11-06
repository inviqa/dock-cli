<?php

use Dock\Doctor;
use Dock\Installer\DNS;
use Dock\Installer\System;
use Dock\Installer\TaskProvider;
use Dock\System\Linux\ShellCreator;

$container['system.shell_creator'] = function() {
    return new ShellCreator();
};

$container['installer.dns.dnsdock'] = function($c) {
    return new DNS\Linux\DnsDock($c['console.user_interaction'], $c['process.interactive_runner']);
};

$container['installer.task_provider'] = function ($c) {
    return new TaskProvider([
        new System\Linux\NoSudo($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Linux\Docker($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Linux\DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
        $c['installer.dns.dnsdock'],
        $c['installer.dns.docker_routing'],
    ]);
};
