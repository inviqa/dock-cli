<?php

namespace Dock\Cli;

use Dock\Compose\Config;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner, Config $config)
    {
        parent::__construct();

        $this->processRunner = $processRunner;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run a command on a service')
            ->addOption(
                'service',
                's',
                InputOption::VALUE_REQUIRED,
                'Service to run the command on'
            )
            ->addArgument(
                'service_command',
                InputArgument::REQUIRED,
                'Command to run on the current service'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('service') !== null) {
            $service = $input->getOption('service');
        } else {
            $service = $this->config->getCurrentService();
        }

        $command = $input->getArgument('service_command');
        $this->processRunner->run("docker-compose run $service $command");
    }
}
