<?php

namespace Dock\Installer;

class DockerInstaller implements Installable
{
    /**
     * @var TaskProvider
     */
    private $taskProvider;

    /**
     * @param TaskProvider $taskProvider
     */
    public function __construct(TaskProvider $taskProvider)
    {
        $this->taskProvider = $taskProvider;
    }

    /**
     * Start the Docker installation process.
     */
    public function run()
    {
        $this->taskProvider->getChainBuilder()->getRunner()->run();
    }
}
