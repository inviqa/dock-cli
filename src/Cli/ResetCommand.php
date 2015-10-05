<?php

namespace Dock\Cli;

use Dock\Compose\Project;
use Dock\Doctor\CommandFailedException;
use Dock\Doctor\Doctor;
use Dock\IO\Process\InteractiveProcessBuilder;
use Dock\IO\Process\InteractiveProcessManager;
use Dock\IO\UserInteraction;
use Dock\Project\ProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ResetCommand extends Command
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
     * @param Project $project
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
            ->setName('reset')
            ->setDescription('Reset the containers of the project')
            ->addArgument('container', InputArgument::IS_ARRAY, 'Component names')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $containers = $input->getArgument('container');
        $this->projectManager->reset($this->project, $containers);

        $output->writeln([
            'Container(s) successfully reset.'
        ]);
    }
}
