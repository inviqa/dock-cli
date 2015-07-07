<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\IO\SilentProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('up')
            ->setDescription('Start the project')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userInteraction = new ConsoleUserInteraction($input, $output);
        $processRunner = new SilentProcessRunner($userInteraction);

        if (!$this->inHomeDirectory()) {
            $output->writeln(
                '<error>The project have to be in your home directly to be able to share it with the Docker VM</error>'
            );

            return 1;
        }

        $userInteraction->writeTitle('Starting application containers');

        try {
            $processRunner->run(new Process('docker-compose up -d'));
            $userInteraction->writeTitle('Application containers successfully started');
        } catch (ProcessFailedException $e) {
            echo $e->getProcess()->getOutput();

            return 1;
        }

        return $this->getApplication()->run(
            new ArrayInput(['command' => 'ps']),
            $output
        );
    }

    /**
     * @return bool
     */
    private function inHomeDirectory()
    {
        $home = getenv('HOME');
        $pwd = getcwd();

        return substr($pwd, 0, strlen($home)) === $home;
    }
}
