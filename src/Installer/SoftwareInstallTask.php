<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\NamedChainProcessInterface;
use Symfony\Component\Process\Process;

abstract class SoftwareInstallTask extends InstallerTask implements NamedChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $processRunner = $context->getProcessRunner();
        $userInteraction = $context->getUserInteraction();

        $versionCommand = $this->getVersionCommand();
        $versionProcess = new Process($versionCommand);
        $processRunner->run($versionProcess, false);

        if ($versionProcess->isSuccessful()) {
            $userInteraction->write(sprintf('"%s" is already installed', $this->getName()));
        } else {
            $installCommand = $this->getInstallCommand();
            $installProcess = new Process($installCommand);

            $userInteraction->write(sprintf('Installing "%s"', $this->getName()));
            $processRunner->run($installProcess);
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
