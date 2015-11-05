<?php

namespace Dock\Docker\Containers;

interface ContainerDetails
{
    /**
     * @param string $containerId
     *
     * @return Container
     */
    public function findById($containerId);
}
