<?php

namespace Dock\System\Mac;

use Dock\System\ShellCreator as ShellCreatorInterface;

class ShellCreator implements ShellCreatorInterface
{
    public function createNewShell()
    {
        pcntl_exec(getenv('SHELL'));
    }
}
