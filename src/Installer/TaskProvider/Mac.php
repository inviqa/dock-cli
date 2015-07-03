<?php

namespace Dock\Installer\TaskProvider;

use Dock\Installer\DNS\Mac\DnsDock;
use Dock\Installer\DNS\Mac\DockerRouting;
use Dock\Installer\Docker\Dinghy;
use Dock\Installer\System\Mac\BrewCask;
use Dock\Installer\System\Mac\DockerCompose;
use Dock\Installer\System\Mac\Homebrew;
use Dock\Installer\System\Mac\PhpSsh;
use Dock\Installer\System\Mac\Vagrant;
use Dock\Installer\System\Mac\VirtualBox;
use Dock\Installer\TaskProvider as TaskProviderInterface;

class Mac implements TaskProviderInterface
{
    public function getTasks()
    {
        return [
            new Homebrew(),
            new BrewCask(),
            new PhpSsh(),
            new Dinghy(),
            new DockerRouting(),
            new DnsDock(),
            new Vagrant(),
            new VirtualBox(),
            new DockerCompose(),
        ];
    }
}
