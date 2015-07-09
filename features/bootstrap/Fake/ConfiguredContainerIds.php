<?php

namespace Fake;

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
}
