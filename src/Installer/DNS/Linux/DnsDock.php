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
        if (!$this->hasDockerOptions()) {
            $this->userInteraction->writeTitle('Configuring DNS resolution for Docker containers');

            $this->processRunner->run('echo \'DOCKER_OPTS="--bip='.self::IP.'/24 --dns '.self::IP.' --dns=8.8.8.8 --dns=8.8.4.4"\' | sudo tee -a /etc/default/docker');
            $this->processRunner->run('sudo service docker restart');
        }

        $this->processRunner->run('sudo docker start dnsdock || sudo docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp aacebedo/dnsdock:latest-amd64');
    }

    private function hasDockerOptions()
    {
        return $this->processRunner->run('grep "^DOCKER_OPTS" /etc/default/docker', false)->isSuccessful();
    }
}