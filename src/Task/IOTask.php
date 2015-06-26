<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use DockerInstaller\IO\ConsoleHelper;
use SRIO\ChainOfResponsibility\ChainContext;

abstract class IOTask
{
    /**
     * @var ConsoleHelper
     */
    protected $consoleHelper;

    /**
     * @param ChainContext $context
     */
    public function execute(ChainContext $context)
    {
        if (!$context instanceof ConsoleContext) {
            throw new \RuntimeException('Expected console context');
        }

        $this->consoleHelper = ConsoleHelper::fromConsoleContext($context);
        $this->run($context);
    }

    /**
     * @param ConsoleContext $context
     */
    abstract public function run(ConsoleContext $context);
}
