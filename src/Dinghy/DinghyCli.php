<?php

namespace Dock\Dinghy;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DinghyCli
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * Start dinghy.
     *
     * @param int $memory The memory, in MB, allocated to the virtual machine
     *
     * @throws ProcessFailedException
     */
    public function start($memory = null)
    {
        $arguments = ['--no-proxy'];
        if (null !== $memory) {
            $arguments[] = '--memory='.$memory;
        }

        $this->processRunner->run('dinghy up '.implode(' ', $arguments));
    }

    /**
     * Stop dinghy.
     *
     * @throws ProcessFailedException
     */
    public function stop()
    {
        $this->processRunner->run('dinghy halt');
    }

    /**
     * Create the Dinghy VM.
     *
     * @throws ProcessFailedException
     */
    public function create()
    {
        $this->processRunner->run('dinghy create --provider virtualbox');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $process = $this->processRunner->run('dinghy version');
        $output = $process->getOutput();

        if (!preg_match('#([0-9\.]+)#', $output, $matches)) {
            throw new \RuntimeException('Unable to resolve Dinghy\'s version');
        }

        return $matches[1];
    }

    /**
     * @return string
     */
    public function getIp()
    {
        $process = $this->processRunner->run('dinghy ip');
        $dinghyIp = $process->getOutput();

        return trim($dinghyIp);
    }

    /**
     * @return bool
     */
    public function isCreated()
    {
        $process = $this->processRunner->run('dinghy status');
        $output = $process->getOutput();

        return strpos($output, 'VM: not created') === false;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        $process = $this->processRunner->run('dinghy status');
        $output = $process->getOutput();

        return strpos($output, 'VM: running') !== false;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->processRunner->run('dinghy version', false)->isSuccessful();
    }
}
