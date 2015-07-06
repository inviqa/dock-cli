<?php
/**
 * Created by PhpStorm.
 * User: tonypiper
 * Date: 06/07/2015
 * Time: 23:01
 */

namespace Dock\Installer;

use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Symfony\Component\Process\Process;

class MockProcessRunner implements ProcessRunner
{
    /**
     * @param Process $process
     * @param bool    $mustSucceed
     *
     * @return Process
     */
    public function run(Process $process, $mustSucceed = true)
    {
        $this->userInteraction->write('<info>RUN (mock)</info> '.$process->getCommandLine());

        return $process;
    }

    public function setUserInteraction(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }
}
