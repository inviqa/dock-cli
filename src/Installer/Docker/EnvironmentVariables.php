<?php

namespace Dock\Installer\Docker;

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
     * @param EnvironManipulatorFactory $environManipulatorFactory
     * @param UserInteraction $userInteraction
     * @param ProcessRunner $processRunner
     */
    public function  __construct(
        EnvironManipulatorFactory $environManipulatorFactory,
        UserInteraction $userInteraction,
        ProcessRunner $processRunner
    ) {
        $this->environManipulatorFactory = $environManipulatorFactory;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
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

        $userHome = getenv('HOME');
        $environmentVariables = [
            new EnvironmentVariable('DOCKER_HOST', 'tcp://127.0.0.1:2376'),
            new EnvironmentVariable('DOCKER_CERT_PATH', $userHome.'/.dinghy/certs'),
            new EnvironmentVariable('DOCKER_TLS_VERIFY', '1'),
        ];

        $environManipulator = $this->environManipulatorFactory->getSystemManipulator($this->processRunner);

        foreach ($environmentVariables as $environmentVariable) {
            if (!$environManipulator->has($environmentVariable)) {
                $environManipulator->save($environmentVariable);
            }
        }
    }

    /**
     * @return bool
     */
    private function isEnvironmentConfigured()
    {
        return getenv('DOCKER_HOST') == 'tcp://127.0.0.1:2376';
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
}
