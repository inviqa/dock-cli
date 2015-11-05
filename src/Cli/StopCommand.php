<?php

namespace Dock\Cli;

use Dock\Docker\Compose\Project;
use Dock\Project\ProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends Command
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @var Project
     */
    private $project;

    /**
     * @param ProjectManager $projectManager
     * @param Project        $project
     */
    public function __construct(ProjectManager $projectManager, Project $project)
    {
        parent::__construct();

        $this->projectManager = $projectManager;
        $this->project = $project;
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
        $this->projectManager->stop($this->project);
    }
}
