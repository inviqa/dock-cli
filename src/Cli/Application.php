<?php

namespace Dock\Cli;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;

class Application extends ConsoleApplication
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return [
            new HelpCommand(),
            new ListCommand(),
            new SelfUpdateCommand(),

            // Docker commands
            new InstallCommand(),
            new RestartCommand(),

            // Project commands
            new UpCommand(),
            new PsCommand(),
        ];
    }
}
