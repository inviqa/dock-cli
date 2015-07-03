<?php

namespace Dock\Installer\DNS\Linux;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DnsDock extends InstallerTask implements DependentChainProcessInterface
{
    private $processRunner;

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['docker'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dns';
    }

    public function run(InstallContext $context)
    {
        $this->processRunner = $context->getProcessRunner();

        $userInteraction = $context->getUserInteraction();
        $userInteraction->writeTitle('Configure DNS resolution for Docker containers');

        if (! $this->hasDockerOpts($processRunner)) {
            $this->runCommand('echo \'DOCKER_OPTS="--bip=172.17.42.1/24 --dns 172.17.42.1"\' | sudo tee -a /etc/default/docker');
            $this->runCommand('sudo service docker restart');
        }

        $this->runCommand('sudo docker start dnsdock || sudo docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp tonistiigi/dnsdock');
    }

    private function runCommand($command)
    {
        return $this->processRunner->run(new Process($command));
    }

    private function hasDockerOpts($processRunner)
    {
        try {
            $this->runCommand('grep "^DOCKER_OPTS" /etc/default/docker');
            return true;
        } catch (ProcessFailedException $e) {
            return false;
        }
    }
}
