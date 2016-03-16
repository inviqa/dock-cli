<?php

namespace Dock\Installer\DNS\Mac;

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
    public function run()
    {
        $this->userInteraction->writeTitle("Pulling image tonistiigi/dnsdock");
        $this->userInteraction->write("This could take a while when run for the first time");
        $this->processRunner->run('docker pull tonistiigi/dnsdock');
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