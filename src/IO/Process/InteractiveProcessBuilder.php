<?php

namespace Dock\IO\Process;

use Dock\IO\Process\Pipe\NullPipe;
use Dock\IO\Process\Pipe\UserInteractionPipe;
use Dock\IO\Process\WaitStrategy\BasicWait;
use Dock\IO\UserInteraction;
use Symfony\Component\Process\Process;

class InteractiveProcessBuilder
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    public function __construct(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }

    public function getManagerFor($command)
    {
        return new InteractiveProcessManager(
            new InteractiveProcess(
                new Process($command),
                new NullPipe(),
                new BasicWait()
            ),
            $this->userInteraction
        );
    }
}
