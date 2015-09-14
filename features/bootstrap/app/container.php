<?php

use Fake\ConfiguredContainerIds;
use Fake\ContainerDetails;
use Fake\Logs;
use Symfony\Component\Console\Tester\ApplicationTester;
use Test\Compose\PredictableProject;
use Test\Plugins\ExtraHostname\InMemoryResolutionWriter;
use Test\Project\PredictableManager;

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

$container['compose.project'] = function ($c) {
    return new PredictableProject(getcwd());
};

$container['project.manager.docker_compose'] = function($c) {
    return new PredictableManager();
};

$container['plugins.extra_hostname.hostname_resolution_writer'] = function($c) {
    return new InMemoryResolutionWriter();
};

return $container;
