<?php

namespace Dock\Installer\System\Mac;

use Dock\Installer\InstallerTask;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class Machine extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;
    /**
     * @var \Dock\Docker\Machine\Machine
     */
    private $machine;

    /**
     * @param UserInteraction              $userInteraction
     * @param \Dock\Docker\Machine\Machine $machine
     */
    public function __construct(UserInteraction $userInteraction, \Dock\Docker\Machine\Machine $machine)
    {
        $this->userInteraction = $userInteraction;
        $this->machine = $machine;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!$this->machine->isCreated()) {
            $this->machine->create();
        }
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
        return 'machine';
    }
}
