<?php

namespace Dock\Installer\System;

use Dock\Installer\SoftwareInstallTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

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
