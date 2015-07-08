<?php

namespace Dock\IO\Process\WaitStrategy;

use Symfony\Component\Process\Process;

interface WaitStrategy
{
    /**
     * Wait for this process to finish.
     *
     * @param Process $process
     */
    public function wait(Process $process);
}
