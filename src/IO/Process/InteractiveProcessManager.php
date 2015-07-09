<?php

namespace Dock\IO\Process;

use Dock\IO\Process\Pipe\NullPipe;
use Dock\IO\Process\Pipe\UserInteractionPipe;
use Dock\IO\Process\WaitStrategy\TimeoutWait;
use Dock\IO\UserInteraction;

class InteractiveProcessManager
{
    /**
     * @var InteractiveProcess
     */
    private $process;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param UserInteraction $userInteraction
     */
    public function __construct(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }

    /**
     * Attach a process.
     *
     * @param InteractiveProcess $process
     */
    public function setProcess(InteractiveProcess $process)
    {
        $this->process = $process;
    }

    /**
     * Disable the process output.
     *
     */
    public function disableOutput()
    {
        $this->process->updatePipe(new NullPipe());
    }

    /**
     * Enable the process output.
     *
     * @param bool $retroActive
     */
    public function enableOutput($retroActive = false)
    {
        $pipe = new UserInteractionPipe($this->userInteraction);
        if ($retroActive) {
            $pipe->rewind($this->process);
        }

        $this->process->updatePipe($pipe);
    }

    /**
     * Run the given process.
     *
     */
    public function run()
    {
        $this->process->run();
    }
}
