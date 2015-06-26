<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

class Vagrant extends IOTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vagrant';
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
        if (!$this->vagrantInstalled()) {
            $this->consoleHelper->writeTitle('Vagrant is not found, installing it');
            $this->installVagrant();
            $this->consoleHelper->writeTitle('Vagrant successfully installed');
        } else {
            $this->consoleHelper->writeTitle('Vagrant successfully detected');
        }
    }

    private function installVagrant()
    {
        $process = new Process('brew cask install vagrant');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);
    }

    private function vagrantInstalled()
    {
        $process = new Process('vagrant --version');
        $this->consoleHelper->runProcess($process);

        return $process->isSuccessful();
    }
}
