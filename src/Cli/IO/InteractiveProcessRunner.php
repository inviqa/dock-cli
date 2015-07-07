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
     * @param bool $highlightErrors
     *
     * @return callable
     */
    private function getRunningProcessCallback($highlightErrors = true)
    {
        return function ($type, $buffer) use ($highlightErrors) {
            $lines = explode("\n", $buffer);
            $prefix = Process::ERR === $type ?
                ($highlightErrors ? '<error>ERR</error>' : 'ERR')
                : '<question>OUT</question>';

            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $this->userInteraction->write($prefix.' '.$line);
                }
            }
        };
    }
}
