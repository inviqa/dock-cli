<?php
namespace Test\Compose;

use Dock\Compose\Project;

class PredictableProject implements Project
{
    /**
     * @var string
     */
    private $projectBasePath;

    /**
     * @param string $projectBasePath
     */
    public function __construct($projectBasePath)
    {
        $this->projectBasePath = $projectBasePath;
    }

    public function getProjectBasePath()
    {
        return $this->projectBasePath;
    }

    public function getComposeConfigPath()
    {
        // TODO: Implement getComposeConfigPath() method.
    }

    public function getCurrentRelativePath()
    {
        // TODO: Implement getCurrentRelativePath() method.
    }

    /**
     * @param string $projectBasePath
     */
    public function setProjectBasePath($projectBasePath)
    {
        $this->projectBasePath = $projectBasePath;
    }

    /**
     * Isolate the project by creating a temporary directory.
     *
     */
    public function isolate()
    {
        $directory = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), uniqid('dock-cli-test')]);
        if (!mkdir($directory)) {
            throw new \RuntimeException(sprintf(
                'Unable to create directory "%s"',
                $directory
            ));
        }

        $this->projectBasePath = $directory;
    }
}