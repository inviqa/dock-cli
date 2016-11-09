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
        $imageName = $this->stripSlashFromImageName($imageName);

        return [
            $imageName.'.docker',
            $containerName.'.'.$imageName.'.docker',
        ];
    }

    /**
     * @param string $imageName
     *
     * @return string
     */
    private function stripTagNameFromImageName($imageName)
    {
        if (false !== ($position = strpos($imageName, ':'))) {
            $imageName = substr($imageName, 0, $position);
        }

        return $imageName;
    }
    
    private function stripSlashFromImageName($imageName)
    {
        if (false !== ($position = strrpos($imageName, '/'))) {
            $imageName = substr($imageName, $position+1 );
        }

        return $imageName;
    }
}
