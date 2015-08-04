<?php

namespace Dock\Doctor;

use Dock\IO\ProcessRunner;
use Dock\Installer\InstallerTask;

class DnsDock
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var mixed
     */
    private $dnsDockInstaller;

    /**
     * @var mixed
     */
    private $dockerRouting;

    /**
     * @param ProcessRunner $processRunner
     * @param mixed $dnsDockInstaller
     * @param mixed $dockerRouting
     */
    public function __construct(ProcessRunner $processRunner, $dnsDockInstaller, $dockerRouting)
    {
        $this->processRunner = $processRunner;
        $this->dnsDockInstaller = $dnsDockInstaller;
        $this->dockerRouting = $dockerRouting;
    }

    /**
     * {@inheritdoc}
     */
    public function run($dryRun)
    {
        $this->handle(
            "isDnsDockRunning",
            "Command `docker ps -q --filter=name=dnsdock` did not return any results - seems dnsdock is not running.",
            "Install and start dnsdock by running: `dock-cli docker:install`",
            $this->dnsDockInstaller,
            $dryRun
        );

        $this->handle(
            "canReachDnsdockContainer",
            "Command `ping -c1 dnsdock.docker` failed - it seems your dns is not set up properly.",
             "Add 172.17.42.1 as one of your DNS servers. `dock-cli docker:install` will try to do that",
            $this->dockerRouting,
            $dryRun
        );
    }

    protected function isDnsdockRunning()
    {
        return null !== $this->processRunner->run('docker ps -q --filter=name=dnsdock')->getOutput();
    }

    protected function canReachDnsdockContainer()
    {
        try {
            $this->processRunner->run('ping -c1 dnsdock.docker');
        } catch (ProcessFailedException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $method Method to check whether problem exists
     * @param string $problem Problem description
     * @param string $suggestedSolution Suggested solution
     * @param InstallerTask $installerTask Task to fix the problem
     * @param bool $dryRun Try to fix the problem?
     */
    public function handle($method, $problem, $suggestedSolution, InstallerTask $installerTask, $dryRun)
    {
        if (! $this->$method()) {
            $exception = new \Exception("$problem\n$suggestedSolution");

            if ($dryRun) {
                throw $exception;
            } else {
                $installerTask->run();

                if (! $this->$method()) {
                    throw $exception;
                }
            }
        }
    }
}
