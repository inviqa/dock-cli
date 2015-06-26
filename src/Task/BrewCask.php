<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Process\Process;

class BrewCask extends IOTask implements DependentChainProcessInterface
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
    public function run(ConsoleContext $context)
    {
        if (!$this->brewCaskInstalled()) {
            $this->consoleHelper->writeTitle('Installing Brew Cask');
            $this->installBrewCask();
            $this->consoleHelper->writeTitle('Successfully installed brew cask');
        } else {
            $this->consoleHelper->writeTitle('Brew Cask already installed, skipping.');
        }
    }

    private function installBrewCask()
    {
        $process = new Process('brew install caskroom/cask/brew-cask');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);

        return $process;
    }

    private function brewCaskInstalled()
    {
        $process = new Process('brew cask --version');
        $this->consoleHelper->runProcess($process);

        return $process->isSuccessful();
    }
}
