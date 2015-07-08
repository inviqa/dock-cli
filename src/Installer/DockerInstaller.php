<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\ChainBuilder;

class DockerInstaller
{
    /**
     * @var InstallContext
     */
    private $context;

    /**
     * @var ChainBuilder
     */
    private $tasks;

    /**
     * @param InstallContext $context
     * @param ChainBuilder $tasks
     */
    public function __construct(InstallContext $context, ChainBuilder $tasks)
    {
        $this->context = $context;
        $this->tasks = $tasks;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $this->tasks->getRunner()->run($this->context);
    }
}
