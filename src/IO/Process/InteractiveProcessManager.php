<?php

namespace Dock\IO\Process;

use Dock\IO\Process\Pipe\NullPipe;
use Dock\IO\Process\Pipe\UserInteractionPipe;
use Dock\IO\Process\WaitStrategy\TimeoutWait;
use Dock\IO\UserInteraction;

class InteractiveProcessManager
{
    private $process;
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    public function __construct(InteractiveProcess $process, UserInteraction $userInteraction)
    {
        $this->process = $process;
        $this->userInteraction = $userInteraction;
    }

    public function disableOutput()
    {
        $this->process->updatePipe(new NullPipe());

        return $this;
    }

    public function enableOutput($retroActive = false)
    {
        $pipe = new UserInteractionPipe($this->userInteraction);
        if ($retroActive) {
            $pipe->rewind($this->process);
        }

        $this->process->updatePipe($pipe);

        return $this;
    }

    public function ifTakesMoreThan($milliSeconds, callable $callable)
    {
        $this->process->updateWaitStrategy(new TimeoutWait($milliSeconds, $callable));

        return $this;
    }

    public function run()
    {
        $this->process->run();
    }
}
