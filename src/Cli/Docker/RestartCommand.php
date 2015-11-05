<?php

namespace Dock\Cli\Docker;

use Dock\Dinghy\DinghyCli;
use Dock\Docker\Machine\Machine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RestartCommand extends Command
{
    /**
     * @var Machine
     */
    private $machine;

    /**
     * @param Machine $machine
     */
    public function __construct(Machine $machine)
    {
        parent::__construct();

        $this->machine = $machine;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:restart')
            ->setDescription('Restart Docker')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->machine->isRunning()) {
            $this->machine->stop();
        }

        $this->machine->start();
    }
}
