<?php

namespace Dock\IO\Process;

use Dock\IO\Process\Pipe\NullPipe;
use Dock\IO\Process\Pipe\Pipe;
use Dock\IO\Process\Pipe\UserInteractionPipe;
use Dock\IO\Process\WaitStrategy\BasicWait;
use Dock\IO\Process\WaitStrategy\TimeoutWait;
use Dock\IO\Process\WaitStrategy\WaitStrategy;
use Dock\IO\UserInteraction;
use Symfony\Component\Process\Process;

class InteractiveProcessBuilder
{
    /**
     * @var InteractiveProcessManager
     */
    private $manager;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @var string
     */
    private $command;

    /**
     * @var Pipe
     */
    private $pipe;

    /**
     * @var WaitStrategy
     */
    private $waitStrategy;

    /**
     * @param UserInteraction $userInteraction
     */
    public function __construct(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }

    /**
     * Set the process command.
     *
     * @param string $command
     *
     * @return InteractiveProcessBuilder
     */
    public function forCommand($command)
    {
        $this->command = $command;
        $this->pipe = new NullPipe();
        $this->manager = new InteractiveProcessManager($this->userInteraction);

        return $this;
    }

    /**
     * @return InteractiveProcessBuilder
     */
    public function disableOutput()
    {
        $this->pipe = new NullPipe();

        return $this;
    }

    /**
     * Makes the process to call the given callback after the given time.
     *
     * @param int $milliSeconds
     * @param callable $callback
     *
     * @return InteractiveProcessBuilder
     */
    public function ifTakesMoreThan($milliSeconds, callable $callback)
    {
        $manager = $this->manager;
        $this->waitStrategy = new TimeoutWait($milliSeconds, function () use ($callback, $manager) {
            $callback($manager);
        });

        return $this;
    }

    /**
     * Get generated process manager.
     *
     * @return InteractiveProcessManager
     */
    public function getManager()
    {
        $this->manager->setProcess(new InteractiveProcess(
            new Process($this->command),
            $this->pipe,
            $this->waitStrategy
        ));

        return $this->manager;
    }
}
