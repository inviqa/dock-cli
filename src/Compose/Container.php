<?php

namespace Dock\Compose;

class Container
{
    const STATE_UNKNOWN = 'unknown';
    const STATE_RUNNING = 'running';
    const STATE_EXITED = 'exited';

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $image;
    /**
     * @var array
     */
    private $hosts;
    /**
     * @var string
     */
    private $state;
    /**
     * @var array
     */
    private $ports;

    /**
     * @param string $name
     * @param string $image
     * @param string $state
     * @param array  $hosts
     * @param array  $ports
     */
    public function __construct($name, $image, $state = self::STATE_UNKNOWN, array $hosts = [], array $ports = [])
    {
        $this->name = $name;
        $this->image = $image;
        $this->hosts = $hosts;
        $this->state = $state;
        $this->ports = $ports;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function getPorts()
    {
        return $this->ports;
    }
}
