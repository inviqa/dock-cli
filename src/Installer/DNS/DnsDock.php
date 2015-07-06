<?php

namespace Dock\Installer\DNS;

use Dock\Dinghy\SshClient;
use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Process;

class DnsDock extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var \Ssh\Exec
     */
    private $sshExec;

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $userInteraction = $context->getUserInteraction();
        $userInteraction->writeTitle('Configure DNS resolution for Docker containers');

        // Configure virtual machine
        $sshClient = new SshClient();
        $this->sshExec = $sshClient->getExec();

        if (!$this->hasDockerExtraArgs()) {
            $this->sshExec->run('echo EXTRA_ARGS=\"-H unix:///var/run/docker.sock --bip=172.17.42.1/16 --dns=172.17.42.1\" | sudo tee -a /var/lib/boot2docker/profile');
        }

        if (!$this->dnsDockerIsInStartupConfiguration()) {
            $bootScript = 'sleep 5'.PHP_EOL.
                'docker start dnsdock || docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp tonistiigi/dnsdock'.PHP_EOL;

            $this->sshExec->run('echo "'.$bootScript.'" | sudo tee -a /var/lib/boot2docker/bootlocal.sh');
        }

        // Configure host machine resolution
        $processRunner = $context->getProcessRunner();
        $processRunner->run(new Process('sudo mkdir -p /etc/resolver'));
        $processRunner->run(new Process('echo "nameserver 172.17.42.1" | sudo tee /etc/resolver/docker'));

        // Restart dinghy
        $userInteraction->writeTitle('Restarting Dinghy');
        $processRunner->run(new Process('dinghy restart'));
    }

    /**
     * @return bool
     */
    private function dnsDockerIsInStartupConfiguration()
    {
        try {
            $result = $this->sshExec->run('grep dnsdock /var/lib/boot2docker/bootlocal.sh');
        } catch (\RuntimeException $e) {
            $result = null;
        }

        return !empty($result);
    }

    /**
     * @return bool
     */
    private function hasDockerExtraArgs()
    {
        try {
            $result = $this->sshExec->run('grep EXTRA_ARGS /var/lib/boot2docker/profile');
        } catch (\RuntimeException $e) {
            $result = null;
        }

        return !empty($result);
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['dinghy'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dns';
    }
}
