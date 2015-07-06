<?php

namespace Dock\Dinghy;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Boot2DockerCli
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
     * Uninstall boot2docker.
     *
     * @return bool
     */
    public function uninstall()
    {
        $scriptPath = $this->getUninstallScriptPath();
        chmod($scriptPath, 0777);

        try {
            $this->processRunner->run(new Process('sudo '.$scriptPath));
        } catch (ProcessFailedException $e) {
            return false;
        }

        return true;
    }

    /**
     * Is boot2docker installed ?
     *
     * @return bool
     */
    public function isInstalled()
    {
        try {
            $this->getVersion();
            return true;
        } catch (ProcessFailedException $e) {
            return false;
        }
    }

    /**
     * Get boot2docker version.
     *
     * @throws ProcessFailedException
     * @return string
     */
    public function getVersion()
    {
        $process = $this->processRunner->run(new Process('boot2docker version'));

        return $process->getOutput();
    }

    /**
     * @return string
     */
    private function getUninstallScriptPath()
    {
        return __DIR__.'/Resources/boot2docker-uninstaller.sh';
    }
}
