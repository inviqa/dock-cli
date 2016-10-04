<?php

namespace Dock\Installer\DNS\Mac;

use Dock\Docker\Machine\DockerMachineCli;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class DownloadDnsDock extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var DockerMachineCli
     */
    private $machine;

    /**
     * @param UserInteraction $userInteraction
     * @param ProcessRunner $processRunner
     * @param DockerMachineCli $machine
     */
    public function __construct(UserInteraction $userInteraction, ProcessRunner $processRunner, DockerMachineCli $machine)
    {
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
        $this->machine = $machine;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->userInteraction->writeTitle("Pulling image aacebedo/dnsdock");
        $this->userInteraction->write("Check docker machine is running");
        if (!$this->machine->isRunning()) {
            $this->userInteraction->write("Starting machine");
            $this->machine->start();
        }
        $this->userInteraction->write("Setting environment variables");
        $this->processRunner->run($this->machine->getEnvironmentDeclaration());

        $this->userInteraction->write("Pulling image aacebedo/dnsdock, this could take a while when run for the first time");
        $this->processRunner->run('docker pull aacebedo/dnsdock:latest-amd64');
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['machine'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dnsdock_image';
    }
}
