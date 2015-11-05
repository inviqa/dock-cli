<?php

namespace Dock\Installer\System\Mac;

use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Console\Question\Question;

class PhpSsh extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param UserInteraction        $userInteraction
     * @param \Dock\IO\ProcessRunner $processRunner
     */
    public function __construct(UserInteraction $userInteraction, ProcessRunner $processRunner)
    {
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
    }

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
    public function run()
    {
        $this->userInteraction->writeTitle('Checking PHP SSH2 extension');

        if ($this->sshExtensionInstalled()) {
            return;
        }

        $this->userInteraction->write('PHP SSH2 extension is required.');

        $package = $this->promptForPackageName();

        if ($package == 'n') {
            $this->userInteraction->write('Skipping PHP SSH2 extension installation, do it yourself.');
        } else {
            $this->installHomebrewPackage($package);
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
     * @param string $package
     *
     * @return bool
     */
    private function hasHomebrewPackage($package)
    {
        return $this->processRunner->run('brew install --dry-run '.$package, false)->isSuccessful();
    }

    /**
     * @return bool
     */
    private function sshExtensionInstalled()
    {
        return function_exists('ssh2_exec');
    }

    /**
     * @param $package
     */
    protected function installHomebrewPackage($package)
    {
        // Check if the package is known
        if (!$this->hasHomebrewPackage($package)) {
            $this->userInteraction->write(sprintf('Package "%s" not found, tapping default PHP brews', $package));

            $this->processRunner->run('brew tap homebrew/dupes');
            $this->processRunner->run('brew tap homebrew/versions');
            $this->processRunner->run('brew tap homebrew/homebrew-php');
        }

        $this->processRunner->run('brew install '.$package);
    }

    /**
     * @return string
     */
    protected function promptForPackageName()
    {
        $defaultPhpVersion = $this->guessSsh2PhpPackage();
        $question = new Question(
            sprintf(
                'Which homebrew package do you want to install (default "%s") ? ("n" for nothing)',
                $defaultPhpVersion
            ),
            $defaultPhpVersion
        );
        $package = $this->userInteraction->ask($question);

        return $package;
    }
}
