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
        $this->userInteraction->writeTitle('Setting up machine environment variables');
        $this->saveEnvironmentVariables();
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
        $environmentDeclaration = $this->dockerMachineCli->getEnvironmentDeclaration();

        if (!$environManipulator->has($environmentDeclaration)) {
            $environManipulator->save($environmentDeclaration);
        }
    }
}
