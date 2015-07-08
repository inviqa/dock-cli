<?php

namespace Dock\Cli;

use Dock\Compose\ComposeExecutableFinder;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StopCommand extends Command
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;

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
     * @param UserInteraction $userInteraction
     * @param ProcessRunner $processRunner
     */
    public function __construct(ComposeExecutableFinder $composeExecutableFinder, UserInteraction $userInteraction, ProcessRunner $processRunner)
    {
        parent::__construct();

        $this->userInteraction = $userInteraction;
        $this->composeExecutableFinder = $composeExecutableFinder;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('stop')
            ->setDescription('Stop the project')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userInteraction->writeTitle('Stopping application containers');

        $this->processRunner->followsUpWith($this->composeExecutableFinder->find(), ['stop']);
    }
}
