<?php

namespace Dock\Doctor;

use Dock\Installer\Installable;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

abstract class Task
{
    /**
     * @var ProcessRunner
     */
    protected $processRunner;

    /**
     * @param OutputInterface $output
     * @param bool $dryRun
     */
    abstract public function run(OutputInterface $output, $dryRun);

    /**
     * @param OutputInterface $output
     * @param string $command Command to check whether a problem exists
     * @param string $workingMessage Output when the command passes
     * @param string $problem Problem description
     * @param string $suggestedSolution Suggested solution
     * @param Installable $installable Task to fix the problem
     * @param bool $dryRun Try to fix the problem?
     */
    protected function handle(OutputInterface $output, $command, $working, $problem, $suggestedSolution, Installable $installable, $dryRun)
    {
        if ($this->testCommand($command)) {
            $output->writeLn("- <info>$working</info>");
        } else {
            if ($dryRun) {
                $output->writeLn("- <error>$problem</error>");
                throw new CommandFailedException("Command $command failed. $problem\n$suggestedSolution");
            } else {
                $output->writeLn("- <error>$problem, attempting to fix that!</error>");
                $installable->run();
                $this->handle($output, $command, $working, $problem, $suggestedSolution, $installable, true);
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
