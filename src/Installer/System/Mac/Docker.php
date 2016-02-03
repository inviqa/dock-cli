<?php

namespace Dock\Installer\System\Mac;

use Dock\Installer\SoftwareInstallTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class Docker extends SoftwareInstallTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dockerMachine';
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
        return 'docker --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        return 'brew install docker';
    }
}
