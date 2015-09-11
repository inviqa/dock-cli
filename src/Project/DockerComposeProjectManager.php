<?php

namespace Dock\Project;

use Dock\Compose\ComposeExecutableFinder;
use Dock\IO\Process\InteractiveProcessBuilder;
use Dock\IO\Process\InteractiveProcessManager;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;

class DockerComposeProjectManager implements ProjectManager
{
    /**
     * @var InteractiveProcessBuilder
     */
    private $interactiveProcessBuilder;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var ComposeExecutableFinder
     */
    private $composeExecutableFinder;

    /**
     * @param InteractiveProcessBuilder $interactiveProcessBuilder
     * @param UserInteraction $userInteraction
     * @param ProcessRunner $processRunner
     * @param ComposeExecutableFinder $composeExecutableFinder
     */
    public function __construct(InteractiveProcessBuilder $interactiveProcessBuilder, UserInteraction $userInteraction, ProcessRunner $processRunner, ComposeExecutableFinder $composeExecutableFinder)
    {

        $this->interactiveProcessBuilder = $interactiveProcessBuilder;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
        $this->composeExecutableFinder = $composeExecutableFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->userInteraction->writeTitle('Starting application containers');

        $this->interactiveProcessBuilder
            ->forCommand(sprintf(
                '%s up -d',
                $this->composeExecutableFinder->find()
            ))
            ->disableOutput()
            ->withoutTimeout()
            ->ifTakesMoreThan(5000, function (InteractiveProcessManager $processManager) {
                $processManager->enableOutput(true);
            })
            ->getManager()
            ->run();
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->userInteraction->writeTitle('Stopping application containers');

        $this->processRunner->followsUpWith($this->composeExecutableFinder->find(), ['stop']);
    }
}
