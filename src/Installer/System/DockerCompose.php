<?php

namespace Dock\Installer\System;

use Dock\Installer\SoftwareInstallTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class DockerCompose extends SoftwareInstallTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dockerCompose';
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
        return 'docker-compose --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        return 'brew install --ignore-dependencies docker-compose';
    }
}
