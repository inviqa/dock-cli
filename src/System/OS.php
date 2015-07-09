<?php

namespace Dock\System;

class OS
{
    const MAC = 1;
    const LINUX = 2;
    const WINDOWS = 3;

    /**
     * @return int Current Operating System
     */
    public function get()
    {
        $uname = strtolower(php_uname('s'));

        switch ($uname) {
            case 'darwin':
                return self::MAC;
            case 'linux':
                return self::LINUX;
            case 'win':
                return self::WINDOWS;
            default:
                throw new \Exception("'$uname' is not a known operating system.");
        }
    }

    /**
     * @return string
     */
    public function getLinuxDistro()
    {
        if ($this->get() !== self::LINUX) {
            throw new \Exception('Not Linux');
        }

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
