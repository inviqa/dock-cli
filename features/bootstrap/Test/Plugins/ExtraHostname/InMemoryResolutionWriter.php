<?php

namespace Test\Plugins\ExtraHostname;

use Dock\Plugins\ExtraHostname\HostnameResolutionWriter;

class InMemoryResolutionWriter implements HostnameResolutionWriter
{
    private $resolutions = [];

    /**
     * {@inheritdoc}
     */
    public function write($hostname, $address)
    {
        $this->resolutions[$hostname] = $address;
    }

    /**
     * @return array
     */
    public function getResolutions()
    {
        return $this->resolutions;
    }
}
