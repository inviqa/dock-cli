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
        $command = sprintf(
            'echo "export %s=%s" >> %s',
            $environmentVariable->getName(),
            $environmentVariable->getValue(),
            $this->file
        );

        $this->processRunner->run($command);
    }

    /**
     * {@inheritdoc}
     */
    public function has(EnvironmentVariable $environmentVariable)
    {
        $process = $this->processRunner->run(
            sprintf('grep %s=%s %s', $environmentVariable->getName(), $environmentVariable->getValue(), $this->file),
            false
        );

        $result = $process->getOutput();

        return !empty($result);
    }
}
