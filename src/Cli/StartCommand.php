<?php

namespace Dock\Cli;

use Dock\Docker\Compose\Project;
use Dock\Project\ProjectException;
use Dock\Project\ProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
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
            ->setName('start')
            ->setDescription('Start the project')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->projectManager->start($this->project);
        } catch (ProjectException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return 1;
        }

        return $this->getApplication()->run(
            new ArrayInput(['command' => 'ps']),
            $output
        );
    }
}
