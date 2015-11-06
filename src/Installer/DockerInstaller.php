<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\ChainRunner;
use SRIO\ChainOfResponsibility\DecoratorFactoryInterface;

class DockerInstaller implements Installable
{
    /**
     * @var TaskProvider
     */
    private $taskProvider;

    /**
     * @var DecoratorFactoryInterface
     */
    private $processDecoratorFactory;

    /**
     * @param TaskProvider              $taskProvider
     * @param DecoratorFactoryInterface $processDecoratorFactory
     */
    public function __construct(TaskProvider $taskProvider, DecoratorFactoryInterface $processDecoratorFactory)
    {
        $this->taskProvider = $taskProvider;
        $this->processDecoratorFactory = $processDecoratorFactory;
    }

    /**
     * Start the Docker installation process.
     */
    public function run()
    {
        $processes = $this->taskProvider->getChainBuilder()->getOrderedProcesses();

        $runner = new ChainRunner($processes, $this->processDecoratorFactory);
        $runner->run();
    }
}
