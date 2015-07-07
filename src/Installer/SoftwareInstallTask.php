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
        $processRunner = $context->getProcessRunner();
        $userInteraction = $context->getUserInteraction();

        if ($processRunner->run($this->getVersionCommand(), false)->isSuccessful()) {
            $userInteraction->write(sprintf('"%s" is already installed', $this->getName()));
        } else {
            $userInteraction->write(sprintf('Installing "%s"', $this->getName()));
            $processRunner->run($this->getInstallCommand());
            $userInteraction->write(sprintf('"%s" successfully installed', $this->getName()));
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
