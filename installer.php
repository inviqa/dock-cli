#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use DockerInstaller\InstallCommand;

$application = new Application();
$application->add(new InstallCommand());
$application->run();
