<?php

namespace Dock\Installer\TaskProvider;

use Dock\Installer\DNS\Linux\DnsDock;
use Dock\Installer\System\Linux\Docker;
use Dock\Installer\System\Linux\DockerCompose;
use Dock\Installer\System\Linux\NoSudo;
use Dock\Installer\TaskProvider as TaskProviderInterface;

class Linux implements TaskProviderInterface
{
    public function getTasks()
    {
        return [
            new Docker(),
            new NoSudo(),
            new DockerCompose(),
            new DnsDock(),
        ];
    }
}
