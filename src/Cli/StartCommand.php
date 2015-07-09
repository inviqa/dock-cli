<?php

namespace Dock\Cli;

use Dock\IO\Process\InteractiveProcessBuilder;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StartCommand extends Command
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param ProcessRunner $processRunner
     * @param UserInteraction $userInteraction
     */
    public function __construct(ProcessRunner $processRunner, UserInteraction $userInteraction)
    {
        parent::__construct();

        $this->processRunner = $processRunner;
        $this->userInteraction = $userInteraction;
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
        if (!$this->inHomeDirectory()) {
            $output->writeln(
                '<error>The project have to be in your home directly to be able to share it with the Docker VM</error>'
            );

            return 1;
        }

        $this->userInteraction->writeTitle('Starting application containers');

        $interactiveProcessBuilder = new InteractiveProcessBuilder($this->userInteraction);
        $processManager = $interactiveProcessBuilder->getManagerFor('docker-compose up -d');
        $processManager
            ->disableOutput()
            ->ifTakesMoreThan(5000, function() use ($processManager) {
                $processManager->enableOutput(true);
            })
            ->run()
        ;

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
