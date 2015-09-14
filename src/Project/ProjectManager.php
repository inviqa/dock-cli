<?php

namespace Dock\Project;

use Dock\Compose\Project;

interface ProjectManager
{
    /**
     * Start the project.
     *
     * @param Project $project
     */
    public function start(Project $project);

    /**
     * Stop the current project.
     *
     * @param Project $project
     */
    public function stop(Project $project);
}
