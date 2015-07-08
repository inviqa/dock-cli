<?php

namespace Dock\IO\Process\WaitStrategy;

use Symfony\Component\Process\Process;

class TimeoutWait implements WaitStrategy
{
    const TICK = 100;

    /**
     * Timeout, in milliseconds.
     *
     * @var int
     */
    private $timeout;

    /**
     * @var callable
     */
    private $callback;

    public function __construct($timeout, callable $callback)
    {
        $this->timeout = $timeout;
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function wait(Process $process)
    {
        $start = microtime(true);
        $end = $start + $this->timeout / 1000;

        while (!$process->isTerminated() && (microtime(true) < $end)) {
            usleep(self::TICK * 1000);
        }

        if ($process->isRunning()) {
            $callback = $this->callback;
            $callback();
        }

        $process->wait();
    }
}
