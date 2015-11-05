<?php

namespace Dock\Cli;

use Dock\Docker\Compose\Config;
use Dock\Docker\Compose\NotWithinServiceException;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

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
     * @param Config        $config
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
                InputArgument::IS_ARRAY,
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
            try {
                $service = $this->config->getCurrentService();
            } catch (NotWithinServiceException $e) {
                $service = $this->askForTheService($input, $output);
            }
        }

        $command = implode(' ', $input->getArgument('service_command'));
        $this->processRunner->run("docker-compose run $service $command");
    }

    private function askForTheService(InputInterface $input, OutputInterface $output)
    {
        $services = $this->config->getServices();

        $question = new ChoiceQuestion(
            'Please select the service to run your command on',
            array_combine($services, $services) // work around a bug in symfony/console 2.7
        );

        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
