<?php

namespace Dock\Installer\DNS\Linux;

use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class DnsDock extends InstallerTask implements DependentChainProcessInterface
{
    const IP = '172.17.42.1';

    /**
     * @var ProcessRunner
     */
    private $processRunner;
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param UserInteraction $userInteraction
     * @param ProcessRunner   $processRunner
     */
    public function __construct(UserInteraction $userInteraction, ProcessRunner $processRunner)
    {
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
    }

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

    public function run()
    {
        $ip = $this->getDockerIp();

        $this->processRunner->run('sudo docker start dnsdock || sudo docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p '.$ip.':53:53/udp tonistiigi/dnsdock');
    }

    private function getDockerIp()
    {
        $output = $this->processRunner->run("ip addr show docker0 | grep 'inet ' | awk -F\\  '{print $2}' | awk '{print $1}'");
        $network = explode('/', trim($output->getOutput()));

        return $network[0];
    }
}
