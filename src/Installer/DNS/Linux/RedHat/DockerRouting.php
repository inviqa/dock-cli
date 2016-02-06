<?php

namespace Dock\Installer\DNS\Linux\RedHat;

use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;

class DockerRouting extends InstallerTask
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
    public function getName()
    {
        return 'routing';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!$this->isUsingDnsDockDnsServer()) {
            $this->userInteraction->writeTitle('Configure routing for direct Docker containers access');

            $this->processRunner->run("sudo sed -i -e '1inameserver ".$this->getDockerIp()."\\' /etc/resolv.conf");
        }
    }

    private function isUsingDnsDockDnsServer()
    {
        return $this->processRunner->run('grep "'.$this->getDockerIp().'" /etc/resolv.conf', false)->isSuccessful();
    }

    private function getDockerIp()
    {
        $output = $this->processRunner->run("ip addr show docker0 | grep 'inet ' | awk -F\\  '{print $2}' | awk '{print $1}'");
        $network = explode('/', trim($output));

        return $network[0];
    }
}
