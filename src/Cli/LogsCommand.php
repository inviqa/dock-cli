<?php

namespace Dock\Cli;

use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class LogsCommand extends Command
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

        pcntl_exec($this->getDockerComposePath(), $composeLogsArguments);
    }

    /**
     * @return string
     */
    private function getDockerComposePath()
    {
        $output = $this->processRunner->run(new Process('which docker-compose'))->getOutput();

        return trim($output);
    }
}
