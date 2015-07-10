<?php

namespace Dock\System\Linux;

use Dock\System\ShellCreator as ShellCreatorInterface;

class ShellCreator implements ShellCreatorInterface
{
    public function createNewShell()
    {
        pcntl_exec('/usr/bin/sudo', array('su', get_current_user()));
    }
}
