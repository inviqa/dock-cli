<?php

namespace Dock\Cli\IO;

use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Symfony\Component\Process\Process;

class InteractiveProcessRunner implements ProcessRunner
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param UserInteraction $userInteraction
     */
    public function __construct(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }

    /**
     * {@inheritdoc}
     */
    public function run($command, $mustSucceed = true)
    {
        $process = new Process($command);

        $this->userInteraction->write('<info>RUN</info> '.$process->getCommandLine());

        if ($mustSucceed) {
            $process->setTimeout(null);

            return $process->mustRun($this->getRunningProcessCallback($mustSucceed));
        }

        $process->run($this->getRunningProcessCallback($mustSucceed));

        return $process;
    }

    /**
     * {@inheritdoc}
     */
    public function followsUpWith($command, array $arguments = [])
    {
        $this->userInteraction->write(sprintf(
            '<info>RUN</info> %s %s',
            $command,
            implode(' ', $arguments)
        ));

        pcntl_exec($command, $arguments);
    }

    /**
     * @param bool $highlightErrors
     *
     * @return callable
     */
    private function getRunningProcessCallback($highlightErrors = true)
    {
        return function ($type, $buffer) use ($highlightErrors) {
            $nonEmptyLines = array_filter(array_map('trim', explode("\n", $buffer)));

            foreach ($nonEmptyLines as $line) {
                $this->userInteraction->write($this->prefix($type, $highlightErrors).$line);
            }
        };
    }

    /**
     * @param string $type
     * @param bool $highlightErrors
     * @return string
     */
    private function prefix($type, $highlightErrors)
    {
        if (Process::ERR !== $type) {
            return '<question>OUT</question> ';
        }

        if ($highlightErrors) {
            return '<error>ERR</error> ';
        }

        return 'ERR ';
    }
}
