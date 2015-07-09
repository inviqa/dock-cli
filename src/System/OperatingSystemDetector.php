<?php

namespace Dock\System;

class OperatingSystemDetector
{
    const MAC = 1;
    const LINUX = 2;
    const WINDOWS = 3;

    private $os;
    private $distro;

    public function __construct()
    {
        $this->os = strtolower(php_uname('s'));

        if ($this->isLinux()) {
            $this->distro = $this->getLinuxDistro();
        }
    }

    public function getOperatingSystem()
    {
        return $this->os;
    }

    public function getLinuxDistribution()
    {
        return $this->distro;
    }

    public function isMac()
    {
        return $this->os === 'darwin';
    }

    public function isLinux()
    {
        return $this->os === 'linux';
    }

    public function isDebian()
    {
        return in_array($this->distro, [
            'debian',
            'ubuntu',
            'linuxmint',
            'elementary os',
            'kali',
        ]);
    }

    public function isRedHat()
    {
        return in_array($this->distro, [
            'redhat',
            'amzn',
            'fedora',
            'centos',
        ]);
    }

    /**
     * @return string
     */
    private function getLinuxDistro()
    {
        exec('command -v lsb_release > /dev/null 2>&1', $out, $return);
        if ($return === 0 && false) {
            $distro = shell_exec('lsb_release -si');
        } else if (file_exists('/etc/lsb-release')) {
            $distro = shell_exec('. /etc/lsb-release && echo "$DISTRIB_ID"');
        } else if (file_exists('/etc/debian_version')) {
            $distro = 'debian';
        } else if (file_exists('/etc/fedora-release')) {
            $distro = 'fedora';
        } else if (file_exists('/etc/os-release')) {
            $distro = shell_exec('. /etc/os-release && echo "$ID"');
        } else {
            throw new \Exception('Unknown distribution');
        }

        return strtolower(trim($distro));
    }
}
