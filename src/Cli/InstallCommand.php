<?php

namespace Dock\Cli;

use Dock\Installer\DockerInstaller;
use Dock\System\ShellCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var DockerInstaller
     */
    private $dockerInstaller;

    /**
     * @var ShellCreator
     */
    private $shellCreator;

    /**
     * @param DockerInstaller $dockerInstaller
     * @param ShellCreator    $shellCreator
     */
    public function __construct(DockerInstaller $dockerInstaller, ShellCreator $shellCreator)
    {
        parent::__construct();

        $this->dockerInstaller = $dockerInstaller;
        $this->shellCreator = $shellCreator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:install')
            ->setDescription('Install Docker')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dockerInstaller->run();
        $this->shellCreator->createNewShell();
    }
}
