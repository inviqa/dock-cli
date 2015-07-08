<?php

namespace Dock\Installer;

use Dock\System\OS;

class TaskProviderFactory
{
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function getProvider()
    {
        $operatingSystem = OS::get();

        switch ($operatingSystem) {
            case OS::MAC:
                return $this->providers['mac'];
            case OS::LINUX:
                return $this->providers['linux'];
            default:
                throw new \Exception("Installer does not support operating system '$operatingSystem'");
        }
    }
}
