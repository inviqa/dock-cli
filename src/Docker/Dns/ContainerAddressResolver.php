<?php

namespace Dock\Docker\Dns;

interface ContainerAddressResolver
{
    /**
     * Get DNS addresses for the given container.
     *
     * @param string $containerName
     * @param string $imageName
     *
     * @return string[]
     */
    public function getDnsByContainerNameAndImage($containerName, $imageName);
}
