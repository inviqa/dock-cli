<?php

namespace Dock\Doctor;

use Dock\IO\ProcessRunner;
use Dock\Installer\InstallerTask;

class DnsDock extends Task
{
    /**
     * @var InstallerTask $dnsDockInstaller
     */
    private $dnsDockInstaller;

    /**
     * @var InstallerTask $dockerRouting
     */
    private $dockerRouting;

    /**
     * @param ProcessRunner $processRunner
     * @param InstallerTask $dnsDockInstaller
     * @param InstallerTask $dockerRouting
     */
    public function __construct(
        ProcessRunner $processRunner,
        InstallerTask $dnsDockInstaller,
        InstallerTask $dockerRouting)
    {
        $this->processRunner = $processRunner;
        $this->dnsDockInstaller = $dnsDockInstaller;
        $this->dockerRouting = $dockerRouting;
    }

    /**
     * {@inheritdoc}
     */
    public function run($dryRun)
    {
        $this->handle(
            "test 0 -lt `docker ps -q --filter=name=dnsdock | wc -l`",
            "It seems dnsdock is not running.",
            "Install and start dnsdock by running: `dock-cli docker:install`",
            $this->dnsDockInstaller,
            $dryRun
        );

        $this->handle(
            "ping -c1 dnsdock.docker",
            "It seems your dns is not set up properly.",
             "Add 172.17.42.1 as one of your DNS servers. `dock-cli docker:install` will try to do that",
            $this->dockerRouting,
            $dryRun
        );
    }
}
