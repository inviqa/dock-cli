<?php

namespace Dock\Cli\IO;

use Dock\Docker\Compose\Project;

class LocalProject implements Project
{
    public function getProjectBasePath()
    {
        $baseDir = getcwd();

        do {
            if (is_file($baseDir.self::CONFIG_FILE)) {
                return $baseDir;
            }
            $baseDir = dirname($baseDir);
        } while ($baseDir !== '/');

        throw new \Exception('Did not find `docker-compose.yml` in any parent directories.');
    }

    public function getComposeConfigPath()
    {
        return $this->getProjectBasePath().self::CONFIG_FILE;
    }

    public function getCurrentRelativePath()
    {
        return substr(getcwd(), strlen($this->getProjectBasePath()) + 1);
    }
}
