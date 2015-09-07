<?php

namespace Dock\Docker\Dns;

class DnsDockResolver implements ContainerAddressResolver
{
    /**
     * {@inheritdoc}
     */
    public function getDnsByContainerNameAndImage($containerName, $imageName)
    {
        return [
            $imageName.'.docker',
            $containerName.'.'.$imageName.'.docker',
        ];
    }
}
