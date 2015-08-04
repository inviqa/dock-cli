<?php

namespace Dock\Doctor;

use Dock\IO\ProcessRunner;

class Docker
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
     * @param bool $dryRun
     */
    public function run($dryRun)
    {
        try {
            $this->processRunner->run('docker -v');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `docker -v` failed - it seems docker is not installed.\n"
                . "Install it with `dock-cli docker:install`");
        }

        try {
            $this->processRunner->run('docker info');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `docker info` failed - it seems the docker daemon is not running.\n"
                . "Start it with `sudo service docker start`");
        }
    }
}
