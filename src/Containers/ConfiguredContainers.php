<?php

namespace Dock\Containers;

class ConfiguredContainers
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
     * @return Container[]
     */
    public function findAll()
    {
        return array_map([$this, 'findContainerById'], $this->getRunningContainerIds());
    }

    /**
     * @return array
     */
    private function getRunningContainerIds()
    {
        return $this->configuredContainerIds->findAll();
    }

    /**
     * @param string $containerId
     *
     * @return Container
     */
    private function findContainerById($containerId)
    {
        return $this->containerDetails->findById($containerId);
    }
}
