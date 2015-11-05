<?php

namespace Dock\Docker\Compose;

use Dock\Containers\ConfiguredContainerIds;
use Dock\Containers\ContainerDetails;

class ContainerInspector
{
    /**
     * @var ConfiguredContainerIds
     */
    private $configuredContainerIds;
    /**
     * @var ContainerDetails
     */
    private $containerDetails;

    /**
     * @param ConfiguredContainerIds $configuredContainerIds
     * @param ContainerDetails       $containerDetails
     */
    public function __construct(ConfiguredContainerIds $configuredContainerIds, ContainerDetails $containerDetails)
    {
        $this->configuredContainerIds = $configuredContainerIds;
        $this->containerDetails = $containerDetails;
    }

    /**
     * @param string $name
     *
     * @return \Dock\Containers\Container
     */
    public function findOneByName($name)
    {
        $containerId = $this->configuredContainerIds->findByName($name);

        return $this->containerDetails->findById($containerId);
    }
}
