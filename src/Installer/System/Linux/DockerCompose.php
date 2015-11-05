<?php

namespace Dock\Installer\System\Linux;

use Dock\Installer\SoftwareInstallTask;

class DockerCompose extends SoftwareInstallTask
{
    const VERSION = '1.3.1';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dockerCompose';
    }

    /**
     * {@inheritdoc}
     */
    protected function getVersionCommand()
    {
        return 'docker-compose --version';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstallCommand()
    {
        $file = 'https://github.com/docker/compose/releases/download/'.self::VERSION.'/docker-compose-'.php_uname('s').'-'.php_uname('m');

        return "curl -L $file > /tmp/docker-compose && chmod +x /tmp/docker-compose && sudo mv /tmp/docker-compose /usr/local/bin/docker-compose";
    }
}
