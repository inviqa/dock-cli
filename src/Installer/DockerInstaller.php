<?php

namespace Dock\Installer;

class DockerInstaller
{
    /**
     * @var InstallContext
     */
    private $context;

    /**
     * @var tasks
     */
    private $tasks;

    /**
     * @param InstallContext $context
     * @param TaskProviderFactory $tasks
     */
    public function __construct(InstallContext $context, TaskProviderFactory $tasks)
    {
        $this->context = $context;
        $this->tasks = $tasks;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $this->tasks->getProvider()->getTasks()->getRunner()->run($this->context);
    }
}
