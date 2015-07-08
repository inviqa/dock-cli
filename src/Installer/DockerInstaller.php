<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\ChainBuilder;

class DockerInstaller
{
    /**
     * @var ChainBuilder
     */
    private $tasks;

    /**
     * @param ChainBuilder $tasks
     */
    public function __construct(ChainBuilder $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $this->tasks->getRunner()->run();
    }
}
