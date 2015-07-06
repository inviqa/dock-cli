<?php

namespace Dock\System\Environ;

interface EnvironManipulator
{
    /**
     * Save an environment variable.
     *
     * @param EnvironmentVariable $environmentVariable
     */
    public function save(EnvironmentVariable $environmentVariable);

    /**
     * Check if an environment variable is saved.
     *
     * @param EnvironmentVariable $environmentVariable
     *
     * @return bool
     */
    public function has(EnvironmentVariable $environmentVariable);
}
