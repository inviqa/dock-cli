<?php

namespace Dock\Doctor;

use Dock\IO\ProcessRunner;

class DnsDock
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
        $dnsdockId = $this->processRunner->run('docker ps -q --filter=name=dnsdock')->getOutput();

        if ($dnsdockId === null) {
            throw new \Exception("Command `docker ps -q --filter=name=dnsdock` did not return any results - seems dnsdock is not running.\n"
                . "Start it with `dock-cli docker:install`");
        }

        try {
            $this->processRunner->run('ping -c1 172.17.42.1');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `ping -c1 172.17.42.1` failed - we can't reach the dnsdock container.\n"
                . "TODO? This should never happen.");
        }

        try {
            $this->processRunner->run('ping -c1 dnsdock.docker');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `ping -c1 dnsdock.docker` failed - it seems your dns is not set up properly.\n"
                . "Add 172.17.42.1 as one of your DNS servers. `dock-cli docker:install` will try to do that");
        }
    }
}

