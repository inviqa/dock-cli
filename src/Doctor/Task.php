<?php

namespace Dock\Doctor;

use Dock\Installer\Installable;
use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Exception\ProcessFailedException;

abstract class Task
{
    /**
     * @var ProcessRunner
     */
    protected $processRunner;

    /**
     * @param bool $dryRun
     */
    abstract public function run($dryRun);

    /**
     * @param string $command Command to check whether a problem exists
     * @param string $problem Problem description
     * @param string $suggestedSolution Suggested solution
     * @param Installable $installable Task to fix the problem
     * @param bool $dryRun Try to fix the problem?
     */
    protected function handle($command, $problem, $suggestedSolution, Installable $installable, $dryRun)
    {
        if (! $this->testCommand($command)) {
            if ($dryRun) {
                throw new \Exception("Command $command failed. $problem\n$suggestedSolution");
            } else {
                $installable->run();
                $this->handle($command, $problem, $suggestedSolution, $installable, true);
            }
        }
    }

    /**
     * @param string $command
     * @return bool Did the command succeed?
     */
    protected function testCommand($command)
    {
        try {
            $this->processRunner->run($command);
        } catch (ProcessFailedException $e) {
            return false;
        }

        return true;
    }
}
