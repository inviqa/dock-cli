<?php

namespace Test;

use Fake\ConfiguredContainerIds;
use Fake\ContainerDetails;
use Fake\Logs;
use Symfony\Component\Console\Tester\ApplicationTester;
use Test\Compose\PredictableProject;
use Test\Plugins\ExtraHostname\InMemoryResolutionWriter;

class Container
{
    private $container;

    public function __construct()
    {
        $this->container = require __DIR__.'/../app/container.php';
    }

    /**
     * @return ConfiguredContainerIds
     */
    public function getConfiguredContainerIds()
    {
        return $this->container['containers.configured_container_ids'];
    }

    /**
     * @return ContainerDetails
     */
    public function getContainerDetails()
    {
        return $this->container['containers.container_details'];
    }

    /**
     * @return Logs
     */
    public function getLogs()
    {
        return $this->container['logs'];
    }

    /**
     * @return ApplicationTester
     */
    public function getApplicationTester()
    {
        return $this->container['application_tester'];
    }

    /**
     * @return PredictableProject
     */
    public function getProject()
    {
        return $this->container['compose.project'];
    }

    /**
     * @return InMemoryResolutionWriter
     */
    public function getResolutionWriter()
    {
        return $this->container['plugins.extra_hostname.hostname_resolution_writer'];
    }
}
