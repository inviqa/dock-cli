<?php

namespace Dock\Docker\Compose;

use Dock\Docker\Containers\ConfiguredContainerIds as ConfiguredContainerIdsInterface;
use Dock\Docker\Containers\ContainerDetails;

class ContainerInspector
{
    /**
     * @var ConfiguredContainerIdsInterface
     */
    private $configuredContainerIds;

    /**
     * @var ContainerDetails
     */
    private $containerDetails;

    /**
     * @param ConfiguredContainerIdsInterface $configuredContainerIds
     * @param ContainerDetails                $containerDetails
     */
    public function __construct(ConfiguredContainerIdsInterface $configuredContainerIds, ContainerDetails $containerDetails)
    {
        $this->configuredContainerIds = $configuredContainerIds;
        $this->containerDetails = $containerDetails;
    }

    /**
     * @param string $name
     *
     * @return \Dock\Docker\Containers\Container
     */
    public function findOneByName($name)
    {
        $containerId = $this->configuredContainerIds->findByName($name);

        return $this->containerDetails->findById($containerId);
    }
}
