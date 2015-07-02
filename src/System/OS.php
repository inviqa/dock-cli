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
    public static function get()
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
}
