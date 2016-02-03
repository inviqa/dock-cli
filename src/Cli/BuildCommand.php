<?php

namespace Dock\Cli;

use Dock\Docker\Compose\Project;
use Dock\Project\DockerComposeProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
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
     * @param DockerComposeProjectManager $projectManager
     * @param Project        $project
     */
    public function __construct(DockerComposeProjectManager $projectManager, Project $project)
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
            ->setName('build')
            ->setDescription('Build and Reset the containers of the project')
            ->addArgument('container', InputArgument::IS_ARRAY, 'Component names')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $containers = $input->getArgument('container');
        $this->projectManager->build($this->project, $containers);

        $output->writeln([
            'Container(s) built and successfully reset.',
        ]);
    }
}
