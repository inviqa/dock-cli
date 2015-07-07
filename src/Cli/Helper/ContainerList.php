<?php

namespace Dock\Cli\Helper;

use Dock\Compose\Container;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

class ContainerList
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param Container[] $containers
     */
    public function render(array $containers)
    {
        $table = new Table($this->output);
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