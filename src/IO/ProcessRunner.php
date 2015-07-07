<?php

namespace Dock\IO;

use Symfony\Component\Process\Process;

interface ProcessRunner
{
    /**
     * @param $command
     * @param bool $mustSucceed
     *
     * @return Process
     */
    public function run($command, $mustSucceed = true);
}
