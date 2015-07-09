<?php

namespace Dock\Installer;

class DockerInstaller
{
    /**
     * @var TaskProviderFactory
     */
    private $tasks;

    /**
     * @param TaskProviderFactory $tasks
     */
    public function __construct(TaskProviderFactory $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $this->tasks->getProvider()->getTasks()->getRunner()->run();
    }
}
