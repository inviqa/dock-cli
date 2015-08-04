<?php

namespace Dock\Doctor;

use Dock\IO\ProcessRunner;
use Dock\Installer\DockerInstaller;

class Docker extends Task
{
    /**
     * @var DockerInstaller
     */
    private $dockerInstaller;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner, DockerInstaller $dockerInstaller)
    {
        $this->processRunner = $processRunner;
        $this->dockerInstaller = $dockerInstaller;
    }

    /**
     * @param bool $dryRun
     */
    public function run($dryRun)
    {
        $this->handle(
            "docker -v",
            "It seems docker is not installed.",
            "Install docker with `dock-cli docker:install`",
            $this->dockerInstaller,
            $dryRun
        );

        $this->handle(
            "docker info",
            "It seems docker daemon is not running.",
            "Start it with `sudo service docker start`",
            $this->dockerInstaller,
            $dryRun
        );

        $this->handle(
            "ping -c1 172.17.42.1",
            "We can't reach docker.",
            "Install and start docker by running: `dock-cli docker:install`",
            $this->dockerInstaller,
            $dryRun
        );
    }
}
