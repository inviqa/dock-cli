<?php

namespace Dock\Cli\Helper;

class Project
{
    const CONFIG_FILE = '/docker-compose.yml';

    public function getProjectBasePath()
    {
        $baseDir = getcwd();

        do {
            if (is_file($baseDir . self::CONFIG_FILE)) {
                return $baseDir;
            }
            $baseDir = dirname($baseDir);
        } while ($baseDir !== '/');

        throw new \Exception('Did not find `docker-compose.yml` in any parent directories.');
    }

    public function getComposeConfigPath()
    {
        return $this->getProjectBasePath() . self::CONFIG_FILE;
    }

    public function getCurrentRelativePath()
    {
        return substr(getcwd(), strlen($this->getProjectBasePath()) + 1);
    }
}
