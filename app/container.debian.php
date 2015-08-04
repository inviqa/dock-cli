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

$container['installer.task_provider'] = function ($c) {
    return new TaskProvider([
        new System\Linux\Debian\NoSudo($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Linux\Docker($c['console.user_interaction'], $c['process.interactive_runner']),
        new System\Linux\DockerCompose($c['console.user_interaction'], $c['process.interactive_runner']),
        new Dns\Linux\DnsDock($c['console.user_interaction'], $c['process.interactive_runner']),
        new Dns\Linux\Debian\DockerRouting($c['console.user_interaction'], $c['process.interactive_runner']),
    ]);
};

$container['doctor.tasks'] = function($c) {
    return array(
        new Doctor\Docker($c['process.interactive_runner']),
        new Doctor\DnsDock($c['process.interactive_runner']),
    );
};
