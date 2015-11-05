<?php

namespace Dock\Docker\Compose;

interface Project
{
    const CONFIG_FILE = '/docker-compose.yml';

    public function getProjectBasePath();
    public function getComposeConfigPath();
    public function getCurrentRelativePath();
}
