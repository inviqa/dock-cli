<?php

namespace Dock\IO;

use Symfony\Component\Process\Process;

interface ProcessRunner
{
    /**
     * @param Process $process
     * @param bool    $mustSucceed
     *
     * @return Process
     */
    public function run(Process $process, $mustSucceed = true);

    public function setUserInteraction(UserInteraction $userInteraction);
}
