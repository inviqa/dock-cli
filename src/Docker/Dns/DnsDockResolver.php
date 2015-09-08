<?php

namespace Dock\Docker\Dns;

class DnsDockResolver implements ContainerAddressResolver
{
    /**
     * {@inheritdoc}
     */
    public function getDnsByContainerNameAndImage($containerName, $imageName)
    {
        $imageName = $this->stripTagNameFromImageName($imageName);

        return [
            $imageName.'.docker',
            $containerName.'.'.$imageName.'.docker',
        ];
    }

    /**
     * @param string $imageName
     * @return string
     */
    private function stripTagNameFromImageName($imageName)
    {
        if (false !== ($position = strpos($imageName, ':'))) {
            $imageName = substr($imageName, 0, $position);
        }

        return $imageName;
    }
}
