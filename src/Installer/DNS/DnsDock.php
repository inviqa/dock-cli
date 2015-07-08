<?php

namespace Dock\Installer\DNS;

use Dock\Dinghy\SshClient;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class DnsDock extends InstallerTask implements DependentChainProcessInterface
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
     * @var SshClient
     */
    private $sshClient;

    /**
     * @param SshClient $sshClient
     * @param UserInteraction $userInteraction
     * @param \Dock\IO\ProcessRunner $processRunner
     */
    public function __construct(SshClient $sshClient, UserInteraction $userInteraction, ProcessRunner $processRunner)
    {
        $this->sshClient = $sshClient;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->userInteraction->writeTitle('Configure DNS resolution for Docker containers');

        $needDinghyRestart = $this->configureVirtualMachine();
        $this->configureHostMachineResolution();

        if ($needDinghyRestart) {
            $this->restartDinghy();
        }
    }

    /**
     * @return bool
     */
    private function dnsDockerIsInStartupConfiguration()
    {
        return $this->sshClient->runAndGetBooleanResult('grep dnsdock /var/lib/boot2docker/bootlocal.sh');
    }

    /**
     * @return bool
     */
    private function hasDockerExtraArgs()
    {
        return $this->sshClient->runAndGetBooleanResult('grep EXTRA_ARGS /var/lib/boot2docker/profile');
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

    /**
     * @return bool
     */
    private function configureVirtualMachine()
    {
        $needDinghyRestart = false;

        if (!$this->hasDockerExtraArgs()) {
            $this->sshClient->run(
                'echo EXTRA_ARGS=\"-H unix:///var/run/docker.sock --bip=172.17.42.1/16 --dns=172.17.42.1\" | sudo tee -a /var/lib/boot2docker/profile'
            );
            $needDinghyRestart = true;
        }

        if (!$this->dnsDockerIsInStartupConfiguration()) {
            $bootScript = 'sleep 5'.PHP_EOL .
                'docker start dnsdock || docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp tonistiigi/dnsdock'.PHP_EOL;

            $this->sshClient->run('echo "'.$bootScript.'" | sudo tee -a /var/lib/boot2docker/bootlocal.sh');
            $needDinghyRestart = true;
            return $needDinghyRestart;
        }

        return $needDinghyRestart;
    }

    private function configureHostMachineResolution()
    {
        $this->processRunner->run('sudo mkdir -p /etc/resolver');
        $this->processRunner->run('echo "nameserver 172.17.42.1" | sudo tee /etc/resolver/docker');
    }

    private function restartDinghy()
    {
        $this->userInteraction->writeTitle('Restarting Dinghy');
        $this->processRunner->run('dinghy restart');
    }
}
