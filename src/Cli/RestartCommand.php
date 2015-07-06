<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\Dinghy\DinghyCli;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestartCommand extends Command
{
    /** @var  ProcessRunner */
    private $processRunner;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:restart')
            ->setDescription('Restart Docker');
    }

    public function setProcessRunner(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userInteraction = new ConsoleUserInteraction($input, $output);

        $this->processRunner->setUserInteraction($userInteraction);

        $dinghy = new DinghyCli($this->processRunner);

        if ($dinghy->isRunning()) {
            $dinghy->stop();
        }

        $dinghy->start();
    }
}
