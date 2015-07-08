<?php

namespace Dock\IO\Process\WaitStrategy;

use Symfony\Component\Process\Process;

class BasicWait implements WaitStrategy
{
    /**
     * {@inheritdoc}
     */
    public function wait(Process $process)
    {
        $process->wait();
    }
}
