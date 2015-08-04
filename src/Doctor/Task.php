<?php

namespace Dock\Doctor;

use Dock\Installer\InstallerTask;

abstract class Task
{
    /**
     * @param bool $dryRun
     */
    abstract public function run($dryRun);

    /**
     * @param string $method Method to check whether problem exists
     * @param string $problem Problem description
     * @param string $suggestedSolution Suggested solution
     * @param InstallerTask $installerTask Task to fix the problem
     * @param bool $dryRun Try to fix the problem?
     */
    protected function handle($method, $problem, $suggestedSolution, InstallerTask $installerTask, $dryRun)
    {
        if (! $this->$method()) {
            if ($dryRun) {
                throw new \Exception("$problem\n$suggestedSolution");
            } else {
                $installerTask->run();
                $this->handle($method, $problem, $suggestedSolution, $installerTask, true);
            }
        }
    }
}
