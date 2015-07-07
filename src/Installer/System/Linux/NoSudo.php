<?php

namespace Dock\Installer\System\Linux;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Process;

class NoSudo extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['docker'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'noSudo';
    }

    public function run(InstallContext $context)
    {
        $processRunner = $context->getProcessRunner();

        if (! $this->isSuccessFul('groups | grep docker', $processRunner)) {
            $userInteraction = $context->getUserInteraction();
            $userInteraction->writeTitle('Make docker work without sudo');

            $processRunner->run(new Process('sudo usermod -a -G docker $USER'));
        }
    }
}
