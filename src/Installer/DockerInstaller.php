<?php

namespace Dock\Installer;

class DockerInstaller
{
    /**
     * @var TaskProvider
     */
    private $taskProvider;

    /**
     * @param TaskProviderFactory $taskProvider
     */
    public function __construct(TaskProvider $taskProvider)
    {
        $this->taskProvider = $taskProvider;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $this->taskProvider->getChainBuilder()->getRunner()->run();
    }
}
