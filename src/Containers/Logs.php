<?php

namespace Dock\Containers;

interface Logs
{
    public function displayAll();

    /**
     * @param string $component
     */
    public function displayComponent($component);
}
