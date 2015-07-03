<?php

namespace Dock\System\Environ;

use Dock\IO\ProcessRunner;

class EnvironManipulatorFactory
{
    /**
     * Get environ manipulator based on the current system.
     *
     * @param ProcessRunner $processRunner
     *
     * @return EnvironManipulator
     */
    public function getSystemManipulator(ProcessRunner $processRunner)
    {
        $shell = getenv('SHELL');
        $userHome = getenv('HOME');

        if (strpos($shell, 'zsh') !== false) {
            return new BashEnvironManipulator($processRunner, $userHome . '/.zshenv');
        } else if (strpos($shell, 'fish') !== false) {
            return new FishEnvironManipulator();
        }

        return new BashEnvironManipulator($processRunner, $userHome . '/.bash_profile');
    }
}
