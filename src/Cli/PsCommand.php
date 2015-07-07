<?php

namespace Dock\Cli;

use Dock\Cli\Helper\ContainerList;
use Dock\Compose\Inspector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PsCommand extends Command
{
    /**
     * @var Inspector
     */
    private $inspector;

    /**
     * @param Inspector $inspector
     */
    public function __construct(Inspector $inspector)
    {
        parent::__construct();

        $this->inspector = $inspector;
    }

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
        $containers = $this->inspector->getRunningContainers();

        $list = new ContainerList($output);
        $list->render($containers);
    }
}
