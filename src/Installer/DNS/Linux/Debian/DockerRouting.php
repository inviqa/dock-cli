<?php

namespace Dock\Installer\DNS\Linux\Debian;

use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;

class DockerRouting extends InstallerTask
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routing';
    }

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $processRunner = $context->getProcessRunner();
        $userInteraction = $context->getUserInteraction();

        if (! $processRunner->run('grep "' . DnsDock::IP . '" /etc/resolv.conf', false)->isSuccessful()) {
            $userInteraction->writeTitle('Configure routing for direct Docker containers access');

            $processRunner->run('echo "nameserver ' . DnsDock::IP . '" | sudo tee -a /etc/resolvconf/resolv.conf.d/head');
            $processRunner->run('sudo resolvconf -u');
        }
    }
}
