<?php

namespace Dock\Installer\System\Mac;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class PhpSsh extends InstallerTask implements DependentChainProcessInterface
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
    public function run(InstallContext $context)
    {
        $userInteraction = $context->getUserInteraction();
        $userInteraction->writeTitle('Checking PHP SSH2 extension');

        if (!$this->sshExtensionInstalled()) {
            $userInteraction->write('PHP SSH2 extension is required.');

            $question = new Question('Which homebrew package do you want to install ? ("n" for nothing)', 'php55-ssh2');
            $answer = $userInteraction->ask($question);

            if ($answer == 'n') {
                $userInteraction->write('Skipping PHP SSH2 extension installation, do it yourself.');
            } else {
                $processRunner = $context->getProcessRunner();
                $processRunner->run(new Process('brew install '.$answer));
            }

            $userInteraction->write('Please re-run this installation script to have enabled PHP-SSH2 extension');

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
