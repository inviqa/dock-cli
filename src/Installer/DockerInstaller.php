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
    private $chainBuilder;

    /**
     * @param InstallContext $context
     * @param ChainBuilder $chainBuilder
     */
    public function __construct(InstallContext $context, ChainBuilder $chainBuilder)
    {
        $this->context = $context;
        $this->chainBuilder = $chainBuilder;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $this->chainBuilder->getRunner()->run($this->context);
    }
}
