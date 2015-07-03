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
            new InstallCommand(),
            new RestartCommand(),
            new UpdateCommand(),
        ];
    }
}
