<?php

namespace Dock\IO\Process\Pipe;

use Dock\IO\Process\InteractiveProcess;
use Dock\IO\UserInteraction;

class UserInteractionPipe implements Pipe
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
     * Rewind pipe from the beginning of the process.
     *
     * @param InteractiveProcess $process
     */
    public function rewind(InteractiveProcess $process)
    {
        $process = $process->getProcess();

        $this->error($process->getErrorOutput());
        $this->output($process->getOutput());
    }

    /**
     * {@inheritdoc}
     */
    public function error($buffer)
    {
        $this->pipeLinesWithPrefix('<error>ERR</error>', explode("\n", $buffer));
    }

    /**
     * {@inheritdoc}
     */
    public function output($buffer)
    {
        $this->pipeLinesWithPrefix('<question>OUT</question>', explode("\n", $buffer));
    }

    /**
     * @param string $prefix
     * @param array $lines
     */
    private function pipeLinesWithPrefix($prefix, array $lines)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $this->userInteraction->write($prefix.' '.$line);
            }
        }
    }
}
