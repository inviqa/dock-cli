<?php

namespace Dock\Plugins\ExtraHostname;

use Dock\Docker\Compose\Project;
use Symfony\Component\PropertyAccess\PropertyAccess;

class HostnameFromComposerResolver implements HostnameResolver
{
    /**
     * {@inheritdoc}
     */
    public function getExtraHostnameConfigurations(Project $project)
    {
        $composerFile = $this->getComposerJsonFilePath($project);
        if (!file_exists($composerFile)) {
            return [];
        }

        $contents = json_decode(file_get_contents($composerFile), true);
        $accessor = PropertyAccess::createPropertyAccessor();
        $componentsConfiguration = $accessor->getValue($contents, '[extra][dock-cli][extra-hostname]');
        if (!is_array($componentsConfiguration)) {
            return [];
        }

        $configurations = [];
        foreach ($componentsConfiguration as $componentName => $hostNames) {
            if (!is_array($hostNames)) {
                $hostNames = [$hostNames];
            }

            foreach ($hostNames as $hostname) {
                $configurations[] = new HostnameConfiguration($componentName, $hostname);
            }
        }

        return $configurations;
    }

    /**
     * @param Project $project
     *
     * @return string
     */
    private function getComposerJsonFilePath(Project $project)
    {
        return $project->getProjectBasePath().DIRECTORY_SEPARATOR.'composer.json';
    }
}
