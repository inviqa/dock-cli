<?php

namespace Dock\Cli;

use Dock\Docker\Compose\Project;
use Dock\Project\ProjectBuildManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    /**
     * @var ProjectBuildManager
     */
    private $projectBuildManager;

    /**
     * @var Project
     */
    private $project;

    /**
     * @param ProjectBuildManager $projectManager
     * @param Project $project
     */
    public function __construct(ProjectBuildManager $projectBuildManager, Project $project)
    {
        parent::__construct();

        $this->projectBuildManager = $projectBuildManager;
        $this->project = $project;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build and Reset the containers of the project')
            ->addArgument('container', InputArgument::IS_ARRAY, 'Component names');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $containers = $input->getArgument('container');
        $this->projectBuildManager->build($this->project, $containers);

        $output->writeln([
            'Container(s) built and successfully reset.',
        ]);
    }
}
