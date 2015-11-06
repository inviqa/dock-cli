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
     * @param bool            $dryRun
     */
    abstract public function run(OutputInterface $output, $dryRun);

    /**
     * @param OutputInterface $output
     * @param string          $command           Command to check whether a problem exists
     * @param string          $workingMessage    Output when the command passes
     * @param string          $problem           Problem description
     * @param string          $suggestedSolution Suggested solution
     * @param Installable     $installable       Task to fix the problem
     * @param bool            $dryRun            Try to fix the problem?
     *
     * @throws CommandFailedException
     */
    protected function handle(OutputInterface $output, $command, $workingMessage, $problem, $suggestedSolution, Installable $installable, $dryRun)
    {
        if ($this->testCommand($command)) {
            $output->writeLn(sprintf('<info>%s</info>', $workingMessage));
        } else {
            if ($dryRun) {
                $output->writeLn(sprintf('- <error>%s</error>', $problem));
                throw new CommandFailedException(sprintf(
                    'Command %s failed. %s'.PHP_EOL.'%s',
                    $command, $problem, $suggestedSolution
                ));
            } else {
                $output->writeLn(sprintf('- <error>%s, attempting to fix that!</error>', $problem));
                $installable->run();
                $this->handle($output, $command, $workingMessage, $problem, $suggestedSolution, $installable, true);
            }
        }
    }

    /**
     * @param string $command
     *
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
