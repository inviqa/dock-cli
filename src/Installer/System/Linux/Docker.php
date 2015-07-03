<?php

namespace Dock\Installer\System\Linux;

use Dock\Installer\SoftwareInstallTask;

class Docker extends SoftwareInstallTask
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'docker';
    }

    /**
     * {@inheritdoc}
     */
    protected function getVersionCommand()
    {
        return 'docker --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        return 'wget -qO- https://get.docker.com/ | sh && sudo service docker start';
    }
}
