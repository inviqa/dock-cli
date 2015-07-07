<?php

namespace Dock\Installer\System;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
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
        $context->writeTitle('Checking PHP SSH2 extension');

        if (!$this->sshExtensionInstalled()) {
            $context->write('PHP SSH2 extension is required.');

            $defaultPhpVersion = $this->guessSsh2PhpPackage();
            $question = new Question(
                sprintf(
                    'Which homebrew package do you want to install (default "%s") ? ("n" for nothing)',
                    $defaultPhpVersion
                ),
                $defaultPhpVersion
            );
            $package = $context->ask($question);

            if ($package == 'n') {
                $context->write('Skipping PHP SSH2 extension installation, do it yourself.');
            } else {
                $processRunner = $context->getProcessRunner();

                // Check if the package is known
                if (!$this->hasHomebrewPackage($context, $package)) {
                    $context->write(sprintf('Package "%s" not found, tapping default PHP brews', $package));

                    $processRunner->run('brew tap homebrew/dupes');
                    $processRunner->run('brew tap homebrew/versions');
                    $processRunner->run('brew tap homebrew/homebrew-php');
                }

                $processRunner->run('brew install '.$package);
            }

            $context->write('Please re-run this installation script to have enabled PHP-SSH2 extension');

            exit(1);
        }
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
     * @param InstallContext $context
     * @param string $package
     * @return bool
     */
    private function hasHomebrewPackage(InstallContext $context, $package)
    {
        return $context->run('brew install --dry-run '.$package, false)->isSuccessful();
    }

    /**
     * @return bool
     */
    private function sshExtensionInstalled()
    {
        return function_exists('ssh2_exec');
    }
}
