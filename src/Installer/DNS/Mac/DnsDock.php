<?php

namespace Dock\Installer\DNS\Mac;

use Dock\Dinghy\SshClient;
use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class DnsDock extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var \Dock\Dinghy\SshClient
     */
    private $sshClient;

    /**
     * @param SshClient $sshClient
     */
    public function __construct(SshClient $sshClient)
    {
        $this->sshClient = $sshClient;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $context->getUserInteraction()->writeTitle('Configure DNS resolution for Docker containers');

        $needDinghyRestart = $this->configureVirtualMachine();
        $this->configureHostMachineResolution($context);

        if ($needDinghyRestart) {
            $this->restartDinghy($context);
        }
    }

    /**
     * @return bool
     */
    private function dnsDockerIsInStartupConfiguration()
    {
        try {
            $result = $this->sshClient->run('grep dnsdock /var/lib/boot2docker/bootlocal.sh');
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
            $result = $this->sshClient->run('grep EXTRA_ARGS /var/lib/boot2docker/profile');
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

    /**
     * @param InstallContext $context
     * @return ProcessRunner
     */
    private function configureHostMachineResolution(InstallContext $context)
    {
        $processRunner = $context->getProcessRunner();
        $processRunner->run('sudo mkdir -p /etc/resolver');
        $processRunner->run('echo "nameserver 172.17.42.1" | sudo tee /etc/resolver/docker');
    }

    /**
     * @param InstallContext $context
     */
    private function restartDinghy(InstallContext $context)
    {
        $context->getUserInteraction()->writeTitle('Restarting Dinghy');
        $context->getProcessRunner()->run('dinghy restart');
    }
}
