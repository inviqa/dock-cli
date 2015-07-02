<?php

namespace Dock\Installer\Installer;

use Dock\Installer\DNS\DnsDock;
use Dock\Installer\DNS\DockerRouting;
use Dock\Installer\Docker\Dinghy;
use Dock\Installer\System\Mac\BrewCask;
use Dock\Installer\System\Mac\DockerCompose;
use Dock\Installer\System\Mac\Homebrew;
use Dock\Installer\System\Mac\PhpSsh;
use Dock\Installer\System\Mac\Vagrant;
use Dock\Installer\System\Mac\VirtualBox;

class Mac extends Base
{
    private function getTasks()
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
