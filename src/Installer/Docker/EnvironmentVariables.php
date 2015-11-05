<?php

namespace Dock\Installer\Docker;

use Dock\Dinghy\DinghyCli;
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
     * @var DinghyCli
     */
    private $dinghy;

    /**
     * @param EnvironManipulatorFactory $environManipulatorFactory
     * @param UserInteraction           $userInteraction
     * @param ProcessRunner             $processRunner
     * @param DinghyCli                 $dinghy
     */
    public function __construct(
        EnvironManipulatorFactory $environManipulatorFactory,
        UserInteraction $userInteraction,
        ProcessRunner $processRunner,
        DinghyCli $dinghy
    ) {
        $this->environManipulatorFactory = $environManipulatorFactory;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
        $this->dinghy = $dinghy;
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

        $this->userInteraction->writeTitle('Setting up dinghy environment variables');

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
        return ['dinghy'];
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
                $this->dinghy->getIp()
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
