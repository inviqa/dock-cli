<?php

namespace Dock\Project;

use Dock\Docker\Compose\Project;

interface ProjectBuildManager
{
    /**
     * Build and reset the containers of the given project.
     *
     * If the list of containers is empty, it'll build and reset all the project's containers.
     *
     * @param Project $project
     * @param array   $containers
     */
    public function build(Project $project, array $containers = []);
}
