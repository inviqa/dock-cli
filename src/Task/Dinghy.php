<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\ChainProcessInterface;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Process;

class Dinghy extends IOTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(ConsoleContext $context)
    {
        if (!$this->dinghyInstalled()) {
            $this->consoleHelper->writeTitle('Installing Dinghy');
            $this->installDinghy();
            $this->consoleHelper->writeTitle('Successfully installed Dinghy');
        } else {
            $this->consoleHelper->writeTitle('Dinghy already installed, skipping.');
        }

        $this->changeDinghyDnsResolverNamespace();

        $this->consoleHelper->writeTitle('Starting up Dinghy');
        if (!$this->dinghyRunning()) {
            $this->startUp();
            $this->consoleHelper->writeTitle('Started Dinghy');
        } else {
            $this->consoleHelper->writeTitle('Dinghy already started');
        }

        if (!$this->haveDinghyEnvironmentVariables()) {
            $this->consoleHelper->writeTitle('Setting up dinghy environment variables');
            $this->setupDinghyEnvironmentVariables();
        }
    }

    private function startUp()
    {
        $process = new Process('dinghy up');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);
    }

    private function installDinghy()
    {
        $process = new Process('brew install https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);
    }

    private function dinghyRunning()
    {
        $process = new Process('dinghy status');
        $this->consoleHelper->runProcess($process, true);
        $output = $process->getOutput();

        return strpos($output, 'VM: running') !== false;
    }

    private function dinghyInstalled()
    {
        $process = new Process('dinghy version');
        $this->consoleHelper->runProcess($process);

        return $process->isSuccessful();
    }

    private function haveDinghyEnvironmentVariables()
    {
        return getenv('DOCKER_HOST') !== false;
    }

    private function setupDinghyEnvironmentVariables()
    {
        $userHome = getenv('HOME');
        $dinghyIp = $this->getDinghyIp();
        $exports = <<<EOF
export DOCKER_HOST=tcp://{$dinghyIp}:2376
export DOCKER_CERT_PATH={$userHome}/.dinghy/certs
export DOCKER_TLS_VERIFY=1
EOF;

        if ($this->isUsingZsh()) {
            $environmentFile = $userHome . '/.zshenv';
        } else {
            $environmentFile = $userHome . '/.bash_profile';
        }

        $process = new Process('grep DOCKER_HOST '.$environmentFile);
        $this->consoleHelper->runProcess($process);
        $result = $process->getOutput();

        if (empty($result)) {
            $process = new Process('echo "'.$exports.'" >> '.$environmentFile);
            $this->consoleHelper->runProcess($process, true);

            exec('source '.$environmentFile);
        }
    }

    private function getDinghyIp()
    {
        $process = new Process('dinghy ip');
        $this->consoleHelper->runProcess($process, true);
        $dinghyIp = $process->getOutput();

        return trim($dinghyIp);
    }

    private function isUsingZsh()
    {
        $shell = getenv('SHELL');

        return strpos($shell, 'zsh') !== false;
    }

    private function changeDinghyDnsResolverNamespace()
    {
        $process = new Process('dinghy version');
        $this->consoleHelper->runProcess($process, true);
        $dinghyVersionOutput = $process->getOutput();
        $dinghyVersion = substr(trim($dinghyVersionOutput), strlen('Dinghy '));
        $dnsMasqConfiguration = '/usr/local/Cellar/dinghy/'.$dinghyVersion.'/cli/dinghy/dnsmasq.rb';

        $process = new Process('sed -i \'\' \'s/docker/dinghy/\' '.$dnsMasqConfiguration);
        $this->consoleHelper->runProcess($process, true);
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
