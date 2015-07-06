<?php

namespace Dock\Installer\System;

use Dock\Installer\SoftwareInstallTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class VirtualBox extends SoftwareInstallTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'virtualbox';
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['brewCask'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getVersionCommand()
    {
        return 'VBoxManage --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        return 'brew cask install virtualbox';
    }
}
