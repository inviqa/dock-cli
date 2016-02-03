<?php

namespace Test\Project;

use Dock\Docker\Compose\Project;
use Dock\Project\ProjectBuildManager;
use Dock\Project\ProjectManager;

class PredictableManager implements ProjectManager, ProjectBuildManager
{
    /**
     * {@inheritdoc}
     */
    public function start(Project $project)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function stop(Project $project)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function reset(Project $project, array $containers = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function build(Project $project, array $containers = [])
    {
    }
}
