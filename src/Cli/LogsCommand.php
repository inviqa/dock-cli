<?php

namespace Dock\Cli;

use Dock\Compose\ComposeExecutableFinder;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogsCommand extends Command
{
    /**
     * @var ComposeExecutableFinder
     */
    private $composeExecutableFinder;
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ComposeExecutableFinder $composeExecutableFinder
     * @param ProcessRunner $processRunner
     */
    public function __construct(ComposeExecutableFinder $composeExecutableFinder, ProcessRunner $processRunner)
    {
        parent::__construct();

        $this->composeExecutableFinder = $composeExecutableFinder;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('logs')
            ->setDescription('Follow logs of application containers')
            ->addArgument('component', InputArgument::OPTIONAL, 'Name of component to follow')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composeLogsArguments = ['logs'];
        if (null !== ($component = $input->getArgument('component'))) {
            $composeLogsArguments[] = $component;
        }

        $this->processRunner->followsUpWith($this->composeExecutableFinder->find(), $composeLogsArguments);
    }
}
