<?php

namespace Dock\IO;

use Symfony\Component\Process\Process;

class SilentProcessRunner implements ProcessRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(Process $process, $mustSucceed = true)
    {
        if ($mustSucceed) {
            $process->setTimeout(null);

            return $process->mustRun();
        }

        $process->run();

        return $process;
    }
}
