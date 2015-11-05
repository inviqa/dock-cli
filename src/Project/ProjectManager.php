<?php

namespace Dock\Project;

use Dock\Docker\Compose\Project;

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

    /**
     * Reset the containers of the given project.
     *
     * If the list of containers is empty, it'll reset all the project's containers.
     *
     * @param Project $project
     * @param array   $containers
     */
    public function reset(Project $project, array $containers = []);
}
