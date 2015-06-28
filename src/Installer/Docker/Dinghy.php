<?php

namespace Dock\Installer\Docker;

use Dock\Dinghy\DinghyCli;
use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Process;

class Dinghy extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $this->userInteraction = $context->getUserInteraction();
        $this->processRunner = $context->getProcessRunner();

        $dinghy = new DinghyCli($context->getProcessRunner());
        if (!$dinghy->isInstalled()) {
            $this->userInteraction->writeTitle('Installing Dinghy');
            $this->installDinghy();
            $this->userInteraction->writeTitle('Successfully installed Dinghy');
        } else {
            $this->userInteraction->writeTitle('Dinghy already installed, skipping.');
        }

        $this->changeDinghyDnsResolverNamespace();

        $this->userInteraction->writeTitle('Starting up Dinghy');
        if (!$dinghy->isRunning()) {
            $dinghy->start();
            $this->userInteraction->writeTitle('Started Dinghy');
        } else {
            $this->userInteraction->writeTitle('Dinghy already started');
        }

        if (!$this->haveDinghyEnvironmentVariables()) {
            $this->userInteraction->writeTitle('Setting up dinghy environment variables');
            $this->setupDinghyEnvironmentVariables();
        }
    }

    private function installDinghy()
    {
        $this->processRunner->run(new Process('brew install https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb'));
    }

    private function haveDinghyEnvironmentVariables()
    {
        return getenv('DOCKER_HOST') !== false;
    }

    private function setupDinghyEnvironmentVariables()
    {
        $userHome = getenv('HOME');
        $exports = <<<EOF
export DOCKER_HOST=tcp://127.0.0.1:2376
export DOCKER_CERT_PATH={$userHome}/.dinghy/certs
export DOCKER_TLS_VERIFY=1
EOF;

        if ($this->isUsingZsh()) {
            $environmentFile = $userHome . '/.zshenv';
        } else {
            $environmentFile = $userHome . '/.bash_profile';
        }

        $process = new Process('grep DOCKER_HOST '.$environmentFile);
        $this->processRunner->run($process, false);
        $result = $process->getOutput();

        if (empty($result)) {
            $process = new Process('echo "'.$exports.'" >> '.$environmentFile);
            $this->processRunner->run($process);

            exec('source '.$environmentFile);
        }
    }

    private function isUsingZsh()
    {
        $shell = getenv('SHELL');

        return strpos($shell, 'zsh') !== false;
    }

    private function changeDinghyDnsResolverNamespace()
    {
        $process = new Process('dinghy version');
        $this->processRunner->run($process);
        $dinghyVersionOutput = $process->getOutput();
        $dinghyVersion = substr(trim($dinghyVersionOutput), strlen('Dinghy '));
        $dnsMasqConfiguration = '/usr/local/Cellar/dinghy/'.$dinghyVersion.'/cli/dinghy/dnsmasq.rb';

        $process = new Process('sed -i \'\' \'s/docker/zzz-dinghy/\' '.$dnsMasqConfiguration);
        $this->processRunner->run($process);
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['homebrew', 'vagrant', 'virtualbox'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dinghy';
    }
}
