<?php

namespace Dock\Installer\System;

use Dock\Installer\SoftwareInstallTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Process;

class BrewCask extends SoftwareInstallTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'brewCask';
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['homebrew'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getVersionCommand()
    {
        return 'brew cask --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        return 'brew install caskroom/cask/brew-cask';
    }
}
