<?php

namespace Fake;

use Dock\Containers\ContainerNotFound;

class ConfiguredContainerIds implements \Dock\Containers\ConfiguredContainerIds
{
    private $ids = [];

    public function setIds(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->ids;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        if (count($this->ids) == 0) {
            throw new ContainerNotFound();
        }

        return current($this->ids);
    }
}
