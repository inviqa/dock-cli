<?php

namespace Dock\Cli;

use Dock\Installer\DockerInstaller;
use Dock\System\OS;
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
     * @var OS
     */
    private $os;

    /**
     * @param DockerInstaller $dockerInstaller
     * @param ProcessRunner $processRunner
     */
    public function __construct(DockerInstaller $dockerInstaller, OS $os)
    {
        parent::__construct();

        $this->dockerInstaller = $dockerInstaller;
        $this->os = $os;
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
        $this->dockerInstaller->install();
        $this->os->createNewShell();
    }
}
