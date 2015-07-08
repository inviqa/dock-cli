<?php

namespace Dock\Installer\System;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Console\Question\Question;

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

        if ($this->sshExtensionInstalled()) {
            return;
        }

        $userInteraction->write('PHP SSH2 extension is required.');

        $defaultPhpVersion = $this->guessSsh2PhpPackage();
        $question = new Question(
            sprintf(
                'Which homebrew package do you want to install (default "%s") ? ("n" for nothing)',
                $defaultPhpVersion
            ),
            $defaultPhpVersion
        );
        $package = $userInteraction->ask($question);

        if ($package == 'n') {
            $userInteraction->write('Skipping PHP SSH2 extension installation, do it yourself.');
        } else {
            $processRunner = $context->getProcessRunner();

            // Check if the package is known
            if (!$this->hasHomebrewPackage($processRunner, $package)) {
                $userInteraction->write(sprintf('Package "%s" not found, tapping default PHP brews', $package));

                $processRunner->run('brew tap homebrew/dupes');
                $processRunner->run('brew tap homebrew/versions');
                $processRunner->run('brew tap homebrew/homebrew-php');
            }

            $processRunner->run('brew install '.$package);
        }

        throw new \RuntimeException('Please re-run this installation script to have enabled PHP-SSH2 extension');
    }

    /**
     * @return string
     */
    private function guessSsh2PhpPackage()
    {
        $package = 'php55-ssh2';
        if (PHP_VERSION_ID > 50600) {
            $package = 'php56-ssh2';
        }

        return $package;
    }

    /**
     * @param ProcessRunner $processRunner
     * @param string $package
     *
     * @return bool
     */
    private function hasHomebrewPackage(ProcessRunner $processRunner, $package)
    {
        return $processRunner->run('brew install --dry-run '.$package, false)->isSuccessful();
    }

    /**
     * @return bool
     */
    private function sshExtensionInstalled()
    {
        return function_exists('ssh2_exec');
    }
}
