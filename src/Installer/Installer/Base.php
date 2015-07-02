<?php

namespace Dock\Installer\Installer;

use Dock\Installer\InstallContext;
use Dock\Installer\InteractiveProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainBuilder;

abstract class Base
{
    public function install(UserInteraction $userInteraction)
    {
        $tasks = $this->getTasks();
        $builder = new ChainBuilder($tasks);

        $processRunner = new InteractiveProcessRunner($userInteraction);
        $context = new InstallContext($processRunner, $userInteraction);

        $runner = $builder->getRunner();
        $runner->run($context);
    }

    abstract protected function getTasks();
}
