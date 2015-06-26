<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

class VirtualBox extends IOTask implements DependentChainProcessInterface
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
    public function run(ConsoleContext $context)
    {
        if (!$this->virtualBoxInstalled()) {
            $this->consoleHelper->writeTitle('VirtualBox is not found, installing it');
            $this->installVirtualBox();
            $this->consoleHelper->writeTitle('VirtualBox successfully installed');
        } else {
            $this->consoleHelper->writeTitle('VirtualBox successfully detected');
        }
    }

    private function installVirtualBox()
    {
        $process = new Process('brew cask install virtualbox');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);
    }

    private function virtualBoxInstalled()
    {
        $process = new Process('VBoxManage --version');
        $this->consoleHelper->runProcess($process);

        return $process->isSuccessful();
    }
}
