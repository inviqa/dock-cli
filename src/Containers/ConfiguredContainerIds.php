<?php

namespace Dock\Containers;

interface ConfiguredContainerIds
{
    /**
     * @return array
     */
    public function findAll();

    /**
     * @param string $name
     * @throws ContainerNotFound
     * @return string
     */
    public function findByName($name);
}
