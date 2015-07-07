<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\IO\ProcessRunner;
use Dock\IO\SilentProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class LogsCommand extends Command
{
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
        $userInteraction = new ConsoleUserInteraction($input, $output);
        $processRunner = new SilentProcessRunner($userInteraction);

        $composeLogsArguments = ['logs'];
        if (null !== ($component = $input->getArgument('component'))) {
            $composeLogsArguments[] = $component;
        }

        pcntl_exec($this->getDockerComposePath($processRunner), $composeLogsArguments);
    }

    /**
     * @param ProcessRunner $processRunner
     *
     * @return string
     */
    private function getDockerComposePath(ProcessRunner $processRunner)
    {
        $output = $processRunner->run(new Process('which docker-compose'))->getOutput();

        return trim($output);
    }
}
