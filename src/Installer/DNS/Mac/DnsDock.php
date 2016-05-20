<?php

namespace Dock\Installer\DNS\Mac;

use Dock\Docker\Machine\SshClient;
use Dock\Docker\Machine\Machine;
use Dock\Docker\Machine\SshFileManipulator;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Dock\System\Bash\BashFileManipulator;
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
     * @var Machine
     */
    private $machine;

    /**
     * @param SshClient       $sshClient
     * @param UserInteraction $userInteraction
     * @param ProcessRunner   $processRunner
     * @param Machine         $machine
     */
    public function __construct(SshClient $sshClient, UserInteraction $userInteraction, ProcessRunner $processRunner, Machine $machine)
    {
        $this->sshClient = $sshClient;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
        $this->machine = $machine;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->userInteraction->writeTitle('Configure DNS resolution for Docker containers');

        $needMachineRestart = $this->configureVirtualMachine();
        $this->configureHostMachineResolution();

        if ($needMachineRestart) {
            $this->restartMachine();
        }
    }

    /**
     * @return bool
     */
    private function dnsDockerIsInStartupConfiguration()
    {
        return $this->sshClient->runAndCheckOutputWasGenerated('grep dnsdock /var/lib/boot2docker/bootlocal.sh');
    }

    /**
     * @return bool
     */
    private function hasDockerExtraArgs()
    {
        return $this->sshClient->runAndCheckOutputWasGenerated('grep EXTRA_ARGS /var/lib/boot2docker/profile');
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['machine', 'dnsdock_image'];
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
        $needMachineRestart = false;

        $daemonArguments = '-H unix:///var/run/docker.sock --bip=172.17.42.1/16 --dns=172.17.42.1';
        $bashFileManipulator = new BashFileManipulator(
            new SshFileManipulator($this->sshClient, '/var/lib/boot2docker/profile')
        );

        $extraArguments = $bashFileManipulator->getValue('EXTRA_ARGS');

        if (strpos($extraArguments, $daemonArguments) === false) {
            $bashFileManipulator->replaceValue('EXTRA_ARGS', $extraArguments.' '.$daemonArguments);

            $needMachineRestart = true;
        }

        if (!$this->dnsDockerIsInStartupConfiguration()) {
            $bootScript = 'sleep 5'.PHP_EOL.
                'docker start dnsdock || docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp tonistiigi/dnsdock'.PHP_EOL;

            $this->sshClient->run('echo "'.$bootScript.'" | sudo tee -a /var/lib/boot2docker/bootlocal.sh');
            $needMachineRestart = true;
        }

        return $needMachineRestart;
    }

    private function configureHostMachineResolution()
    {
        $this->processRunner->run('sudo mkdir -p /etc/resolver');
        $this->processRunner->run('echo "nameserver 172.17.42.1" | sudo tee /etc/resolver/docker');
    }

    private function restartMachine()
    {
        if ($this->machine->isRunning()) {
            $this->machine->stop();
        }

        $this->machine->start();
    }
}
