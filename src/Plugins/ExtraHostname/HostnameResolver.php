<?php

namespace Dock\Plugins\ExtraHostname;

use Dock\Compose\Project;

interface HostnameResolver
{
    /**
     * Get extra hostname configurations from the given project.
     *
     * @param Project $project
     *
     * @return HostnameConfiguration[]
     */
    public function getExtraHostnameConfigurations(Project $project);
}
