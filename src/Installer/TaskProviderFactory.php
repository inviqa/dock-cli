<?php

namespace Dock\Installer;

use Dock\System\OS;

class TaskProviderFactory
{
    private $providers;

    public function __construct(array $providers, OS $os)
    {
        $this->providers = $providers;
        $this->os = $os;
    }

    public function getProvider()
    {
        $operatingSystem = $this->os->get();

        if ($operatingSystem === OS::MAC) {
            return $this->providers['mac'];
        } elseif ($operatingSystem === OS::LINUX) {
            $distro = $this->os->getLinuxDistro();
            switch ($distro) {
                case 'debian':
                case 'ubuntu':
                case 'linuxmint':
                case 'elementary os':
                case 'kali':
                    return $this->providers['debian'];
                case 'redhat':
                case 'amzn':
                case 'fedora':
                case 'centos':
                    // return $this->providers['redhat']; // doesn't quite work yet
                default:
                    throw new Exception("Linux distribution '$distro' is not supported.");
            }
        } else {
            throw new \Exception("Installer does not support operating system '$operatingSystem'");
        }
    }
}
