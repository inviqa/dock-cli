<?php

namespace Dock\System\Environ;

interface EnvironManipulator
{
    /**
     * Save the environment variable declaration.
     *
     * @param string $declaration
     */
    public function save($declaration);

    /**
     * Check if the given variable declaration is set.
     *
     * @param string $declaration
     *
     * @return bool
     */
    public function has($declaration);
}
