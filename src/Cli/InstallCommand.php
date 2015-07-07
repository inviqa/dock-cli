<?php

namespace Dock\Cli;

use Dock\Installer\DockerInstaller;
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
     * @param DockerInstaller $dockerInstaller
     */
    public function __construct(DockerInstaller $dockerInstaller)
    {
        parent::__construct();

        $this->dockerInstaller = $dockerInstaller;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:install')
            ->setDescription('Install Docker on OSX')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dockerInstaller->install();

        pcntl_exec(getenv('SHELL'));
    }
}
