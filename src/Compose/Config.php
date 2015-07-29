<?php

namespace Dock\Compose;

use Symfony\Component\Yaml\Parser;

class Config
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param string $configPath
     */
    public function __construct($configPath)
    {
        $yaml = new Parser();
        $this->config = $yaml->parse(file_get_contents($configPath));
    }

    /**
     * Return docker-compose service based on directory
     *
     * @param string $directory
     * @return string Service
     */
    public function getCurrentService($directory)
    {
        $servicePaths = $this->getServiceBuildPaths();
        $dirParts = $this->directoryPathToArray($directory);

        do {
            foreach ($servicePaths as $service => $path) {
                if ($path == $dirParts) {
                    return $service;
                }
            }
        } while (array_pop($dirParts));

        throw new \Exception("Directory $directory is not within any known service");
    }

    /**
     * @return array
     */
    private function getServiceBuildPaths()
    {
        return array_map(function($item) {
            return $this->directoryPathToArray($item['build']);
        }, array_filter($this->config, function($item) {
            return array_key_exists('build', $item);
        }));
    }

    /**
     * @return array
     */
    private function directoryPathToArray($path)
    {
        return array_values(array_filter(explode('/', $path), function($item) {
            return !in_array($item, ['', '.']);
        }));
    }
}
