<?php

namespace Dock\Dinghy;

use Dock\IO\PharFileExtractor;
use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Boot2DockerCli
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var PharFileExtractor
     */
    private $fileExtractor;

    /**
     * @param ProcessRunner     $processRunner
     * @param PharFileExtractor $fileExtractor
     */
    public function __construct(ProcessRunner $processRunner, PharFileExtractor $fileExtractor)
    {
        $this->processRunner = $processRunner;
        $this->fileExtractor = $fileExtractor;
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
            $this->processRunner->run('sudo '.$scriptPath);
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
        return $this->processRunner->run('boot2docker version', false)->isSuccessful();
    }

    /**
     * Get boot2docker version.
     *
     * @throws ProcessFailedException
     *
     * @return string
     */
    public function getVersion()
    {
        $process = $this->processRunner->run('boot2docker version');

        return $process->getOutput();
    }

    /**
     * @return string
     */
    private function getUninstallScriptPath()
    {
        return $this->fileExtractor->extract(__DIR__.'/Resources/boot2docker-uninstaller.sh');
    }
}
