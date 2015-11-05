<?php

namespace Dock\Project\Decorator;

use Dock\Docker\Compose\Project;
use Dock\IO\UserInteraction;
use Dock\Project\ProjectException;
use Dock\Project\ProjectManager;

class ProjectShouldBeInHomeDirectory implements ProjectManager
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param ProjectManager  $projectManager
     * @param UserInteraction $userInteraction
     */
    public function __construct(ProjectManager $projectManager, UserInteraction $userInteraction)
    {
        $this->projectManager = $projectManager;
        $this->userInteraction = $userInteraction;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Project $project)
    {
        if (!$this->inHomeDirectory()) {
            throw new ProjectException('The project have to be in your home directly to be able to share it with the Docker VM');
        }

        return $this->projectManager->start($project);
    }

    /**
     * {@inheritdoc}
     */
    public function stop(Project $project)
    {
        return $this->projectManager->stop($project);
    }

    /**
     * {@inheritdoc}
     */
    public function reset(Project $project, array $containers = [])
    {
        return $this->projectManager->reset($project, $containers);
    }

    /**
     * @return bool
     */
    private function inHomeDirectory()
    {
        $home = getenv('HOME');
        $pwd = getcwd();

        return substr($pwd, 0, strlen($home)) === $home;
    }
}
