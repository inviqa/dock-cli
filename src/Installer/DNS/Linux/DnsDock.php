<?php

namespace Dock\Installer\DNS\Linux;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Process;

class DnsDock extends InstallerTask implements DependentChainProcessInterface
{
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
        $processRunner = $context->getProcessRunner();

        if (! $processRunner->run('grep "^DOCKER_OPTS" /etc/default/docker', false)->isSuccessful()) {
            $userInteraction = $context->getUserInteraction();
            $userInteraction->writeTitle('Configure DNS resolution for Docker containers');

            $processRunner->run('echo \'DOCKER_OPTS="--bip=172.17.42.1/24 --dns 172.17.42.1"\' | sudo tee -a /etc/default/docker');
            $processRunner->run('sudo service docker restart');
        }

        $processRunner->run('sudo docker start dnsdock || sudo docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp tonistiigi/dnsdock');
    }
}
