<?php

namespace Dock\Cli;

use Dock\Compose\ComposeExecutableFinder;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Dock\Project\ProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StopCommand extends Command
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @param ProjectManager $projectManager
     */
    public function __construct(ProjectManager $projectManager)
    {
        parent::__construct();

        $this->projectManager = $projectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('stop')
            ->setDescription('Stop the project')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectManager->stop();
    }
}
