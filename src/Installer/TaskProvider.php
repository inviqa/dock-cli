<?php

namespace Dock\Installer;

interface TaskProvider
{
    /**
     * @return array
     */
    public function getTasks();
}
