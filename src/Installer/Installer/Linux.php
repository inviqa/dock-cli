<?php

namespace Dock\Installer\Installer;

use Dock\Installer\System\Linux\Docker;

class Linux extends Base
{
    protected function getTasks()
    {
        return [
            new Docker(),
        ];
    }
}
