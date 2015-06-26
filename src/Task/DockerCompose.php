<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Process\Process;

class DockerCompose extends IOTask implements DependentChainProcessInterface
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
    public function run(ConsoleContext $context)
    {
        if (!$this->dockerComposeInstalled()) {
            $this->consoleHelper->writeTitle('Installing Docker-Compose');
            $this->installDockerCompose();
            $this->consoleHelper->writeTitle('Successfully installed docker-compose');
        } else {
            $this->consoleHelper->writeTitle('docker-compose already installed, skipping.');
        }
    }

    private function installDockerCompose()
    {
        $process = new Process('brew install --ignore-dependencies docker-compose');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);

        return $process;
    }

    private function dockerComposeInstalled()
    {
        $process = new Process('docker-compose --version');
        $this->consoleHelper->runProcess($process);

        return $process->isSuccessful();
    }
}
