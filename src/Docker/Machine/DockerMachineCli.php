<?php

namespace Dock\Docker\Machine;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerMachineCli implements Machine
{
    const DEFAULT_MACHINE_NAME = 'inviqa-dock-cli';

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
    public function __construct(ProcessRunner $processRunner, $name)
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

        return strpos($process->getOutput(), 'Running') === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        try {
            $this->processRunner->run('docker-machine start '.$this->name);
        } catch (ProcessFailedException $e) {
            throw new MachineException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        try {
            $this->processRunner->run('docker-machine stop '.$this->name);
        } catch (ProcessFailedException $e) {
            throw new MachineException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIp()
    {
        try {
            $ip = $this->processRunner->run('docker-machine ip '.$this->name)->getOutput();
        } catch (ProcessFailedException $e) {
            throw new MachineException($e->getMessage(), $e->getCode(), $e);
        }

        $ip = trim($ip);
        if (strpos($ip, 'not running') !== false) {
            throw new MachineException($ip);
        }

        return $ip;
    }

    /**
     * {@inheritdoc}
     */
    public function isCreated()
    {
        $process = $this->processRunner->run('docker-machine status '.$this->name, false);
        if(!$process->isSuccessful() || strpos($process->getOutput(), 'Error') === 0) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        try {
            $this->processRunner->run('docker-machine create --driver virtualbox '.$this->name);
        } catch (ProcessFailedException $e) {
            throw new MachineException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    public function getEnvironmentDeclaration()
    {
        return sprintf('eval "$(docker-machine env %s)"', $this->name).PHP_EOL;
    }
}
