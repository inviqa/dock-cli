<?php

namespace Dock\Installer\Docker;

use Dock\Docker\Machine\Machine;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Dock\System\Environ\EnvironManipulatorFactory;
use Dock\System\Environ\EnvironmentVariable;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class EnvironmentVariables extends InstallerTask implements DependentChainProcessInterface
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
     * @var EnvironManipulatorFactory
     */
    private $environManipulatorFactory;
    /**
     * @var Machine
     */
    private $machine;

    /**
     * @param EnvironManipulatorFactory $environManipulatorFactory
     * @param UserInteraction           $userInteraction
     * @param ProcessRunner             $processRunner
     * @param Machine                   $machine
     */
    public function __construct(
        EnvironManipulatorFactory $environManipulatorFactory,
        UserInteraction $userInteraction,
        ProcessRunner $processRunner,
        Machine $machine
    ) {
        $this->environManipulatorFactory = $environManipulatorFactory;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
        $this->machine = $machine;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->isEnvironmentConfigured()) {
            $this->userInteraction->write('Environment variables are already configured');

            return;
        }

        $this->userInteraction->writeTitle('Setting up machine environment variables');

        $environmentVariables = $this->getEnvironmentVariables();

        $this->saveEnvironmentVariables($environmentVariables);
    }

    /**
     * @return bool
     */
    private function isEnvironmentConfigured()
    {
        foreach ($this->getEnvironmentVariables() as $environmentVariable) {
            if (getenv($environmentVariable->getName()) != $environmentVariable->getValue()) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['dockerMachine'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shell-env';
    }

    /**
     * @return EnvironmentVariable[]
     */
    protected function getEnvironmentVariables()
    {
        $userHome = getenv('HOME');
        $environmentVariables = [
            new EnvironmentVariable('DOCKER_HOST', sprintf(
                'tcp://%s:2376',
                $this->machine->getIp()
            )),
            new EnvironmentVariable('DOCKER_CERT_PATH', $userHome.'/.docker/machine/machines/dinghy'),
            new EnvironmentVariable('DOCKER_TLS_VERIFY', '1'),
            new EnvironmentVariable('DOCKER_MACHINE_NAME', 'dinghy'),
        ];

        return $environmentVariables;
    }

    /**
     * @param $environmentVariables
     */
    protected function saveEnvironmentVariables($environmentVariables)
    {
        $environManipulator = $this->environManipulatorFactory->getSystemManipulator($this->processRunner);

        foreach ($environmentVariables as $environmentVariable) {
            if (!$environManipulator->has($environmentVariable)) {
                $environManipulator->save($environmentVariable);
            }
        }
    }
}
