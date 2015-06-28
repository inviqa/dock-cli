#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Dock\Cli\Application;

$application = new Application();
$application->run();
