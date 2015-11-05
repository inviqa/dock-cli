<?php

namespace Dock\Docker\Machine;

use Dock\IO\ProcessRunner;

class DockerMachineCli implements Machine
{
    const MACHINE_NAME = 'dinghy';

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var string
     */
    private $name;

    /**
     * @param ProcessRunner $processRunner
     * @param string        $name
     */
    public function __construct(ProcessRunner $processRunner, $name = self::MACHINE_NAME)
    {
        $this->processRunner = $processRunner;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        $process = $this->processRunner->run('docker-machine status '.$this->name, false);
        if (!$process->isSuccessful()) {
            return false;
        }

        return $process->getOutput() == 'Running';
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->processRunner->run('docker-machine start '.$this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->processRunner->run('docker-machine stop '.$this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getIp()
    {
        return $this->processRunner->run('docker-machine ip '.$this->name)->getOutput();
    }
}
