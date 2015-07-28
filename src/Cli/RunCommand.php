<?php

namespace Dock\Cli;

use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        parent::__construct();

        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run a command on a service')
            ->addArgument(
                'service',
                InputArgument::REQUIRED,
                'Service to run the command on'
            )
            ->addArgument(
                'service_command',
                InputArgument::REQUIRED,
                'Command to run on the service'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $input->getArgument('service');
        $command = $input->getArgument('service_command');
        $this->processRunner->run("docker-compose run $service $command");
    }
}
