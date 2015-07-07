<?php

namespace Dock\Installer;

use Dock\Installer\DNS\DnsDock;
use Dock\Installer\DNS\DockerRouting;
use Dock\Installer\Docker\Dinghy;
use Dock\Installer\Docker\EnvironmentVariables;
use Dock\Installer\System\BrewCask;
use Dock\Installer\System\DockerCompose;
use Dock\Installer\System\Homebrew;
use Dock\Installer\System\PhpSsh;
use Dock\Installer\System\Vagrant;
use Dock\Installer\System\VirtualBox;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainBuilder;

class DockerInstaller
{
    public function __construct()
    {
    }

    public function install(UserInteraction $userInteraction)
    {
        $tasks = $this->getTasks();
        $builder = new ChainBuilder($tasks);

        $processRunner = new InteractiveProcessRunner($userInteraction);
        $context = new InstallContext($processRunner, $userInteraction);

        $runner = $builder->getRunner();
        $runner->run($context);
    }

    private function getTasks()
    {
        return [
            new Homebrew(),
            new BrewCask(),
            new PhpSsh(),
            new Dinghy(),
            new DockerRouting(),
            new DnsDock(),
            new Vagrant(),
            new VirtualBox(),
            new DockerCompose(),
            new EnvironmentVariables(),
        ];
    }
}
