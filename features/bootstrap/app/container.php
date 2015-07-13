<?php

use Fake\ConfiguredContainerIds;
use Fake\ContainerDetails;
use Fake\Logs;
use Symfony\Component\Console\Tester\ApplicationTester;

$container = require __DIR__ . '/../../../app/container.php';

$container['containers.configured_container_ids'] = function ($c) {
    return new ConfiguredContainerIds();
};

$container['containers.container_details'] = function ($c) {
    return new ContainerDetails();
};

$container['application_tester'] = function ($c) {
    $application = $c['application'];
    $application->setAutoExit(false);

    return new ApplicationTester($application);
};

$container['logs'] = function ($c) {
    return new Logs($c['console.user_interaction']);
};

return $container;
