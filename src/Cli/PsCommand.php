<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Compose\Container;
use Dock\Compose\Inspector;
use Dock\Installer\InteractiveProcessRunner;
use Dock\IO\ProcessRunner;
use Dock\IO\SilentProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ps')
            ->setDescription('List running containers')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userInteraction = new ConsoleUserInteraction($input, $output);
        $processRunner = new SilentProcessRunner($userInteraction);

        $inspector = new Inspector($processRunner);
        $containers = $inspector->getRunningContainers();

        $table = new Table($output);
        $table->setHeaders(['Name', 'DNS addresses', 'Port(s)', 'Status']);

        foreach ($containers as $index => $container) {
            if ($index > 0) {
                $table->addRow(new TableSeparator());
            }

            $table->addRow([
                $container->getName(),
                implode("\n", $container->getHosts()),
                implode("\n", $container->getPorts()),
                $this->getDecoratedState($container)
            ]);
        }

        $table->render();
    }

    /**
     * @param Container $container
     * @return string
     */
    private function getDecoratedState(Container $container)
    {
        if ($container->getState() === Container::STATE_EXITED) {
            return '<error>'.$container->getState().'</error>';
        }

        return $container->getState();
    }
}
