<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class PhpSsh extends IOTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phpSsh';
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
        $this->consoleHelper->writeTitle('Checking PHP SSH2 extension');

        if (!$this->sshExtensionInstalled()) {
            $this->consoleHelper->write('PHP SSH2 extension is required.');

            $questionHelper = new QuestionHelper();
            $question = new Question('Which homebrew package do you want to install ? ("n" for nothing)', 'php55-ssh2');
            $answer = $questionHelper->ask($context->getInput(), $context->getOutput(), $question);

            if ($answer == 'n') {
                $this->consoleHelper->write('Skipping PHP SSH2 extension installation, do it yourself.');
            } else {
                $process = new Process('brew install '.$answer);
                $this->consoleHelper->runProcess($process, true);
            }

            $this->consoleHelper->write('Please re-run this installation script to have enabled PHP-SSH2 extension');

            exit(1);
        }
    }

    /**
     * @return bool
     */
    private function sshExtensionInstalled()
    {
        return function_exists('ssh2_exec');
    }
}
