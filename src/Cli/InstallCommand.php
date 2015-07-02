<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Installer\DockerInstaller;
use Dock\Installer\TaskProvider;
use Dock\System\OS;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Install Docker')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userInteraction = new ConsoleUserInteraction($input, $output);

        $taskProvider = TaskProvider\Factory::getProvider();
        $installer = new DockerInstaller;
        $installer->install($userInteraction, $taskProvider);
    }
}
