<?php

namespace Dock\DockerCompose;

use Dock\Compose\ComposeExecutableFinder;
use Dock\IO\ProcessRunner;

class Logs implements \Dock\Containers\Logs
{
    /**
     * @var ComposeExecutableFinder
     */
    private $composeExecutableFinder;
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ComposeExecutableFinder $composeExecutableFinder
     * @param ProcessRunner $processRunner
     */
    public function __construct(ComposeExecutableFinder $composeExecutableFinder, ProcessRunner $processRunner)
    {
        $this->composeExecutableFinder = $composeExecutableFinder;
        $this->processRunner = $processRunner;
    }

    public function displayAll()
    {
        $this->displayLogs(['logs']);
    }

    /**
     * {@inheritdoc}
     */
    public function displayComponent($component)
    {
        $this->displayLogs(['logs', $component]);
    }

    /**
     * @param $composeLogsArguments
     */
    private function displayLogs($composeLogsArguments)
    {
        $this->processRunner->followsUpWith($this->composeExecutableFinder->find(), $composeLogsArguments);
    }
}
