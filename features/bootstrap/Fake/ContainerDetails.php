<?php

namespace Fake;

use Dock\Containers\Container;

class ContainerDetails implements \Dock\Containers\ContainerDetails
{
    private $states = [];

    public function setState($id, $state, $dns)
    {
        $this->states[$id] = ['state'=> $state, 'hosts' => [$dns]];
    }

    /**
     * @param string $containerId
     * @return Container
     */
    public function findById($containerId)
    {
        if (!array_key_exists($containerId, $this->states)) {
            throw new \RuntimeException('Asking for undefined state');
        }

        return new Container(
            "CONTAINER_$containerId",
            'IMG',
            $this->states[$containerId]['state'],
            $this->states[$containerId]['hosts']
        );
    }
}
