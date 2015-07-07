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
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainBuilder;

class DockerInstaller
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
     * @param ProcessRunner   $processRunner
     * @param UserInteraction $userInteraction
     */
    public function __construct(ProcessRunner $processRunner, UserInteraction $userInteraction)
    {
        $this->processRunner = $processRunner;
        $this->userInteraction = $userInteraction;
    }

    /**
     * Start the Docker installation process.
     */
    public function install()
    {
        $tasks = $this->getTasks();
        $builder = new ChainBuilder($tasks);

        $context = new InstallContext($this->processRunner, $this->userInteraction);

        $runner = $builder->getRunner();
        $runner->run($context);
    }

    /**
     * @return InstallerTask[]
     */
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
