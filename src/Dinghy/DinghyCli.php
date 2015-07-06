<?php

namespace Dock\Dinghy;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
     * Start dingly.
     *
     * @throws ProcessFailedException
     */
    public function start()
    {
        $this->processRunner->run(new Process('dinghy up --no-proxy'));
    }

    /**
     * Stop dingly.
     *
     * @throws ProcessFailedException
     */
    public function stop()
    {
        $this->processRunner->run(new Process('dinghy halt'));
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $process = $this->processRunner->run(new Process('dinghy version'));

        return $process->getOutput();
    }

    /**
     * @return string
     */
    public function getIp()
    {
        $process = $this->processRunner->run(new Process('dinghy ip'));
        $dinghyIp = $process->getOutput();

        return trim($dinghyIp);
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        $process = $this->processRunner->run(new Process('dinghy status'));
        $output = $process->getOutput();

        return strpos($output, 'VM: running') !== false;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->processRunner->run(new Process('dinghy version'), false)->isSuccessful();
    }
}
