<?php

namespace Dock\Doctor\Action;

use Dock\Docker\Machine\Machine;
use Dock\Docker\Machine\MachineException;
use Dock\Installer\DockerInstaller;
use Dock\Installer\Installable;

class StartMachineOrInstall implements Installable
{
    /**
     * @var Machine
     */
    private $machine;

    /**
     * @var DockerInstaller
     */
    private $dockerInstaller;

    /**
     * @param Machine         $machine
     * @param DockerInstaller $dockerInstaller
     */
    public function __construct(Machine $machine, DockerInstaller $dockerInstaller)
    {
        $this->machine = $machine;
        $this->dockerInstaller = $dockerInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        try {
            $this->machine->start();
        } catch (MachineException $e) {
            // Ignore the problem as if the machine is not running, we
            // will run the installer.
        }

        if (!$this->machine->isRunning()) {
            $this->dockerInstaller->run();
        }
    }
}
