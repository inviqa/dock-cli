<?php

namespace Dock\Installer\System;

use Dock\Installer\SoftwareInstallTask;

class Homebrew extends SoftwareInstallTask
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'homebrew';
    }

    /**
     * {@inheritdoc}
     */
    protected function getVersionCommand()
    {
        return 'brew --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        return 'ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"';
    }
}
