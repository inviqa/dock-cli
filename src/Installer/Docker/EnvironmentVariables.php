<?php

namespace Dock\Installer\Docker;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use Dock\System\Environ\EnvironManipulatorFactory;
use Dock\System\Environ\EnvironmentVariable;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class EnvironmentVariables extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var EnvironManipulatorFactory
     */
    private $environManipulatorFactory;

    /**
     * @param EnvironManipulatorFactory $environManipulatorFactory
     */
    public function  __construct(EnvironManipulatorFactory $environManipulatorFactory)
    {
        $this->environManipulatorFactory = $environManipulatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $userInteraction = $context->getUserInteraction();
        if ($this->isEnvironmentConfigured()) {
            $userInteraction->write('Environment variables are already configured');

            return;
        }

        $userInteraction->writeTitle('Setting up dinghy environment variables');
        $processRunner = $context->getProcessRunner();

        $userHome = getenv('HOME');
        $environmentVariables = [
            new EnvironmentVariable('DOCKER_HOST', 'tcp://127.0.0.1:2376'),
            new EnvironmentVariable('DOCKER_CERT_PATH', $userHome.'/.dinghy/certs'),
            new EnvironmentVariable('DOCKER_TLS_VERIFY', '1'),
        ];

        $environManipulator = $this->environManipulatorFactory->getSystemManipulator($processRunner);

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
