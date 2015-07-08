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
     * @param ComposeExecutableFinder $composeExecutableFinder
     * @param UserInteraction $userInteraction
     */
    public function __construct(ComposeExecutableFinder $composeExecutableFinder, UserInteraction $userInteraction)
    {
        parent::__construct();

        $this->userInteraction = $userInteraction;
        $this->composeExecutableFinder = $composeExecutableFinder;
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

        pcntl_exec($this->composeExecutableFinder->find(), ['stop']);
    }
}
