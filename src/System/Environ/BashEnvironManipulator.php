<?php

namespace Dock\System\Environ;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Process;

class BashEnvironManipulator implements EnvironManipulator
{
    /**
     * @var string
     */
    private $file;
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param string $file
     */
    public function __construct(ProcessRunner $processRunner, $file)
    {
        $this->file = $file;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EnvironmentVariable $environmentVariable)
    {
        $command = 'echo "'.$environmentVariable->getName().'='.$environmentVariable->getValue().'" >> '.$this->file;
        $process = new Process($command);

        $this->processRunner->run($process);
    }

    /**
     * {@inheritdoc}
     */
    public function has(EnvironmentVariable $environmentVariable)
    {
        $process = $this->processRunner->run(
            new Process("grep {$environmentVariable->getName()} $this->file"),
            false
        );

        $result = $process->getOutput();

        return !empty($result);
    }
}
