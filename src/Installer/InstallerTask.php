<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\ChainProcessInterface;

abstract class InstallerTask implements ChainProcessInterface, Installable
{
    /**
     * {@inheritdoc}
     */
    public function execute(ChainContext $context)
    {
        $this->run();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function run();
}
