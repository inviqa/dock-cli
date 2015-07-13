<?php

namespace Dock\Cli;

use Dock\Containers\Logs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogsCommand extends Command
{
    /**
     * @var Logs
     */
    private $logs;

    /**
     * @param Logs $logs
     */
    public function __construct(Logs $logs)
    {
        parent::__construct();

        $this->logs = $logs;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('logs')
            ->setDescription('Follow logs of application containers')
            ->addArgument('component', InputArgument::OPTIONAL, 'Name of component to follow');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->getArgument('component')
            ? $this->logs->displayComponent($input->getArgument('component'))
            : $this->logs->displayAll();
    }
}
