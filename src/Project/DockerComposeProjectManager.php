<?php

namespace Dock\Project;

use Dock\Docker\Compose\ComposeExecutableFinder;
use Dock\Docker\Compose\Project;
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
     * @param UserInteraction           $userInteraction
     * @param ProcessRunner             $processRunner
     * @param ComposeExecutableFinder   $composeExecutableFinder
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
    public function start(Project $project)
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
    }

    /**
     * {@inheritdoc}
     */
    public function stop(Project $project)
    {
        $this->userInteraction->writeTitle('Stopping application containers');

        $this->processRunner->followsUpWith($this->composeExecutableFinder->find(), ['stop']);
    }

    /**
     * {@inheritdoc}
     */
    public function reset(Project $project, array $containers = [])
    {
        $composePath = $this->composeExecutableFinder->find();

        $this->processRunner->run(implode(' && ', array_map(function ($action) use ($composePath, $containers) {
            return implode(' ', [
                $composePath,
                $action,
                implode(' ', $containers),
            ]);
        }, ['kill', 'rm -f', 'up -d'])));
    }
}
