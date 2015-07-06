<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Installer\DockerInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    private $dockerInstaller;

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

    public function setDockerInstaller(DockerInstaller $dockerInstaller)
    {
        $this->dockerInstaller = $dockerInstaller;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userInteraction = new ConsoleUserInteraction($input, $output);

        $this->dockerInstaller->install($userInteraction);
    }
}
