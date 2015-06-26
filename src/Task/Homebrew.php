<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Process\Process;

class Homebrew extends IOTask implements NamedChainProcessInterface
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
    public function run(ConsoleContext $context)
    {
        if (!$this->brewInstalled()) {
            $this->consoleHelper->writeTitle('Installing Homebrew');
            $this->installHomebrew();
            $this->consoleHelper->writeTitle('Successfully installed homebrew');
        } else {
            $this->consoleHelper->writeTitle('Homebrew already installed, skipping.');
        }
    }

    private function installHomebrew()
    {
        $process = new Process('ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);
    }

    private function brewInstalled()
    {
        $process = new Process('brew --version');
        $this->consoleHelper->runProcess($process);

        return $process->isSuccessful();
    }
}
