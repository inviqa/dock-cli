<?php

namespace Dock\Installer;

use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainContext;

class InstallContext implements ChainContext
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
     * @param ProcessRunner   $processRunner
     * @param UserInteraction $userInteraction
     */
    public function __construct(ProcessRunner $processRunner, UserInteraction $userInteraction)
    {
        $this->processRunner = $processRunner;
        $this->userInteraction = $userInteraction;
    }

    /**
     * @return ProcessRunner
     */
    public function getProcessRunner()
    {
        return $this->processRunner;
    }

    /**
     * @return UserInteraction
     */
    public function getUserInteraction()
    {
        return $this->userInteraction;
    }
}
