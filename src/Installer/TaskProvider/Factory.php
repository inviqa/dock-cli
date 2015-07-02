<?php

namespace Dock\Installer\TaskProvider;

use Dock\System\OS;

class Factory
{
    public static function getProvider()
    {
        $operatingSystem = OS::get();

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
