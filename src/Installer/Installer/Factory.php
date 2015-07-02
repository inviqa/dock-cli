<?php

namespace Dock\Installer\Installer;

use Dock\System\OS;

class Factory
{
    public function getInstaller(OS $os)
    {
        $operatingSystem = $os->get();

        switch ($operatingSystem) {
            case OS::MAC:
                return new Mac();
            case OS::LINUX:
                return new Linux();
            default:
                throw new \Exception("Installer does not support operating system '$operatingSystem'");
        }
    }
}
