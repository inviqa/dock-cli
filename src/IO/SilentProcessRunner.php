<?php

namespace Dock\IO;

use Symfony\Component\Process\Process;

class SilentProcessRunner implements ProcessRunner
{
    /**
     * {@inheritdoc}
     */
    public function run($command, $mustSucceed = true)
    {
        $process = new Process($command);

        if ($mustSucceed) {
            $process->setTimeout(null);

            return $process->mustRun();
        }

        $process->run();

        return $process;
    }
}
