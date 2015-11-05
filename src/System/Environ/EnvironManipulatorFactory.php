<?php

namespace Dock\System\Environ;

class EnvironManipulatorFactory
{
    /**
     * Get environ manipulator based on the current system.
     *
     * @return EnvironManipulator
     */
    public function getSystemManipulator()
    {
        $shell = getenv('SHELL');
        $userHome = getenv('HOME');

        $environFile = $userHome.'/.bash_profile';
        if (strpos($shell, 'zsh') !== false) {
            $environFile = $userHome.'/.zshenv';
        } elseif (strpos($shell, 'fish') !== false) {
            $environFile = $userHome.'/.config/fish/config.fish';
        }

        return new FileEnvironManipulator($environFile);
    }
}
