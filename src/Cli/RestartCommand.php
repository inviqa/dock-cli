<?php

namespace Dock\Cli;

use Dock\Dinghy\DinghyCli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RestartCommand extends Command
{
    /**
     * @var DinghyCli
     */
    private $dinghy;

    /**
     * @param DinghyCli $dinghy
     */
    public function __construct(DinghyCli $dinghy)
    {
        parent::__construct();

        $this->dinghy = $dinghy;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:restart')
            ->setDescription('Restart Docker')
            ->addOption('memory', 'm', InputOption::VALUE_REQUIRED, 'Memory (in MB) allocated to the Dinghy VM')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->dinghy->isRunning()) {
            $this->dinghy->stop();
        }

        $this->dinghy->start($input->getOption('memory'));
    }
}
