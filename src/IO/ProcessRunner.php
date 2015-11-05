<?php

namespace Dock\IO;

use Symfony\Component\Process\Process;

interface ProcessRunner
{
    /**
     * @param string $command
     * @param bool   $mustSucceed
     *
     * @return Process
     */
    public function run($command, $mustSucceed = true);

    /**
     * @param string $command
     * @param array  $arguments
     */
    public function followsUpWith($command, array $arguments = []);
}
