<?php

namespace Dock\Installer;

use Dock\Installer\InstallContext;
use Dock\Installer\InteractiveProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainBuilder;

class DockerInstaller
{
    public function install(UserInteraction $userInteraction, TaskProvider $taskProvider)
    {
        $builder = new ChainBuilder($taskProvider->getTasks());

        $processRunner = new InteractiveProcessRunner($userInteraction);
        $context = new InstallContext($processRunner, $userInteraction);

        $runner = $builder->getRunner();
        $runner->run($context);
    }
}
