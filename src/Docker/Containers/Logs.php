<?php

namespace Dock\Docker\Containers;

interface Logs
{
    /**
     * Display loads.
     */
    public function displayAll();

    /**
     * @param string $component
     */
    public function displayComponent($component);
}
