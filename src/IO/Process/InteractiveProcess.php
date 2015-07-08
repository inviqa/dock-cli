<?php

namespace Dock\IO\Process;

use Dock\IO\Process\Pipe\Pipe;
use Dock\IO\Process\WaitStrategy\WaitStrategy;
use Symfony\Component\Process\Process;

class InteractiveProcess
{
    /**
     * @var Pipe
     */
    private $pipe;
    /**
     * @var WaitStrategy
     */
    private $waitStrategy;
    /**
     * @var Process
     */
    private $process;

    public function __construct(Process $process, Pipe $pipe, WaitStrategy $waitStrategy)
    {
        $this->pipe = $pipe;
        $this->waitStrategy = $waitStrategy;
        $this->process = $process;
    }

    public function updatePipe(Pipe $pipe)
    {
        $this->pipe = $pipe;
    }

    public function updateWaitStrategy(WaitStrategy $waitStrategy)
    {
        $this->waitStrategy = $waitStrategy;
    }

    public function run()
    {
        $this->process->start(function($type, $buffer) {
            if (Process::ERR === $type) {
                $this->pipe->error($buffer);
            } else {
                $this->pipe->output($buffer);
            }
        });

        $this->waitStrategy->wait($this->process);
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}
