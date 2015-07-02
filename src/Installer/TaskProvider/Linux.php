<?php

namespace Dock\Installer\TaskProvider;

use Dock\Installer\System\Linux\Docker;
use Dock\Installer\TaskProvider as TaskProviderInterface;

class Linux implements TaskProviderInterface
{
    public function getTasks()
    {
        return [
            new Docker(),
        ];
    }
}
