<?php

namespace Dock\Installer\DNS\Linux\Debian;

use Dock\Installer\DNS\Linux\DnsDock;
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
     * @param ProcessRunner $processRunner
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
        if (! $this->processRunner->run('grep "' . DnsDock::IP . '" /etc/resolv.conf', false)->isSuccessful()) {
            $this->userInteraction->writeTitle('Configure routing for direct Docker containers access');

            $this->processRunner->run('echo "nameserver ' . DnsDock::IP . '" | sudo tee -a /etc/resolvconf/resolv.conf.d/head');
            $this->processRunner->run('sudo resolvconf -u');
        }
    }
}
