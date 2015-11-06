<?php

namespace Dock\Doctor;

use Dock\Doctor\Action\StartMachineOrInstall;
use Dock\IO\ProcessRunner;
use Dock\Installer\DockerInstaller;
use Symfony\Component\Console\Output\OutputInterface;

class Docker extends Task
{
    /**
     * @var DockerInstaller
     */
    private $dockerInstaller;

    /**
     * @var StartMachineOrInstall
     */
    private $startMachineOrInstall;

    /**
     * @param ProcessRunner         $processRunner
     * @param StartMachineOrInstall $startMachineOrInstall
     * @param DockerInstaller       $dockerInstaller
     */
    public function __construct(ProcessRunner $processRunner, StartMachineOrInstall $startMachineOrInstall, DockerInstaller $dockerInstaller)
    {
        $this->processRunner = $processRunner;
        $this->dockerInstaller = $dockerInstaller;
        $this->startMachineOrInstall = $startMachineOrInstall;
    }

    /**
     * {@inheritdoc}
     */
    public function run(OutputInterface $output, $dryRun)
    {
        $this->handle(
            $output,
            'docker -v',
            'Docker is installed',
            'It seems docker is not installed.',
            'Install docker with `dock-cli docker:install`',
            $this->dockerInstaller,
            $dryRun
        );

        $this->handle(
            $output,
            'docker info',
            'Docker daemon is running',
            'It seems docker daemon is not running.',
            'Start restarting the Docker service or the VM',
            $this->startMachineOrInstall,
            $dryRun
        );

        $this->handle(
            $output,
            'ping -c1 172.17.42.1',
            'Can ping docker virtual interface',
            "Can't ping docker virtual interface.",
            'Install and start docker by running: `dock-cli docker:install`',
            $this->dockerInstaller,
            $dryRun
        );
    }
}
