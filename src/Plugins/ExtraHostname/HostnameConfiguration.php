<?php

namespace Dock\Plugins\ExtraHostname;

class HostnameConfiguration
{
    /**
     * @var string
     */
    private $container;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @param string $container
     * @param string $hostname
     */
    public function __construct($container, $hostname)
    {
        $this->container = $container;
        $this->hostname = $hostname;
    }

    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }
}
