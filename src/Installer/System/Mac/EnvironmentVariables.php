<?php

namespace Dock\Installer\System\Mac;

use Dock\Docker\Machine\DockerMachineCli;
use Dock\Installer\InstallerTask;
use Dock\IO\UserInteraction;
use Dock\System\Environ\EnvironManipulatorFactory;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class EnvironmentVariables extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @var EnvironManipulatorFactory
     */
    private $environManipulatorFactory;

    /**
     * @var DockerMachineCli
     */
    private $dockerMachineCli;

    /**
     * @param UserInteraction           $userInteraction
     * @param EnvironManipulatorFactory $environManipulatorFactory
     * @param DockerMachineCli          $dockerMachineCli
     */
    public function __construct(UserInteraction $userInteraction, EnvironManipulatorFactory $environManipulatorFactory, DockerMachineCli $dockerMachineCli)
    {
        $this->userInteraction = $userInteraction;
        $this->dockerMachineCli = $dockerMachineCli;
        $this->environManipulatorFactory = $environManipulatorFactory;
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
        $this->saveEnvironmentVariables();
    }

    /**
     * @return bool
     */
    private function isEnvironmentConfigured()
    {
        return getenv('DOCKER_HOST') !== false;
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
        return 'shell-env';
    }

    /**
     * Save the environment variables.
     */
    protected function saveEnvironmentVariables()
    {
        $environManipulator = $this->environManipulatorFactory->getSystemManipulator();
        $environManipulator->save($this->dockerMachineCli->getEnvironmentDeclaration());
    }
}
