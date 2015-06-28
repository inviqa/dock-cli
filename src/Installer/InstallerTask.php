<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\ChainContext;

abstract class InstallerTask
{
    /**
     * @param ChainContext $context
     */
    public function execute(ChainContext $context)
    {
        if (!$context instanceof InstallContext) {
            throw new \RuntimeException('Expected console context');
        }

        $this->run($context);
    }

    /**
     * @param InstallContext $context
     */
    abstract public function run(InstallContext $context);
}
