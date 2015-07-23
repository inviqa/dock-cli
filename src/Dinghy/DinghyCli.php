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
     * @return string
     */
    public function getVersion()
    {
        $process = $this->processRunner->run('dinghy version');

        return $process->getOutput();
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
