<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\NamedChainProcessInterface;

abstract class SoftwareInstallTask extends InstallerTask implements NamedChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        if ($context->run($this->getVersionCommand(), false)->isSuccessful()) {
            $context->write(sprintf('"%s" is already installed', $this->getName()));
        } else {
            $context->write(sprintf('Installing "%s"', $this->getName()));
            $context->run($this->getInstallCommand());
            $context->write(sprintf('"%s" successfully installed', $this->getName()));
        }
    }

    /**
     * @return string
     */
    abstract protected function getVersionCommand();

    /**
     * @return string
     */
    abstract protected function getInstallCommand();
}
