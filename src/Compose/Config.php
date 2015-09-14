<?php

namespace Dock\Compose;

use Symfony\Component\Yaml\Parser;

class Config
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * List docker-compose services
     *
     * @return array
     */
    public function getServices()
    {
        return array_keys($this->getConfig());
    }

    /**
     * Return docker-compose service based on current directory
     *
     * @return string Service
     */
    public function getCurrentService()
    {
        $directory = $this->project->getCurrentRelativePath();
        $servicePaths = $this->getServiceBuildPaths();
        $dirParts = $this->directoryPathToArray($directory);

        do {
            foreach ($servicePaths as $service => $path) {
                if ($path == $dirParts) {
                    return $service;
                }
            }
        } while (array_pop($dirParts));

        throw new NotWithinServiceException("Directory $directory is not within any known service");
    }

    /**
     * @return array
     */
    private function getServiceBuildPaths()
    {
        return array_map(function($item) {
            return $this->directoryPathToArray($item['build']);
        }, array_filter($this->getConfig(), function($item) {
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

    private function getConfig()
    {
        $configPath = $this->project->getComposeConfigPath();
        return (new Parser)->parse(file_get_contents($configPath));
    }
}
