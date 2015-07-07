<?php

namespace Dock\Cli;

use Dock\Cli\Helper\ContainerList;
use Dock\Containers\ConfiguredContainers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PsCommand extends Command
{
    /**
     * @var ConfiguredContainers
     */
    private $configuredContainers;

    /**
     * @param ConfiguredContainers $configuredContainers
     */
    public function __construct(ConfiguredContainers $configuredContainers)
    {
        parent::__construct();

        $this->configuredContainers = $configuredContainers;
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
        $containers = $this->configuredContainers->findAll();

        $list = new ContainerList($output);
        $list->render($containers);
    }
}
