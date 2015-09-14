<?php

namespace Dock\Plugins\ExtraHostname\ProjectManager;

use Dock\Compose\Project;
use Dock\DockerCompose\ContainerInspector;
use Dock\Plugins\ExtraHostname\HostnameResolutionWriter;
use Dock\Plugins\ExtraHostname\HostnameResolver;
use Dock\Project\ProjectManager;

class UpdatesManualDnsNamesOfContainers implements ProjectManager
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @var HostnameResolver
     */
    private $hostnameResolver;

    /**
     * @var HostnameResolutionWriter
     */
    private $hostnameResolutionWriter;

    /**
     * @var ContainerInspector
     */
    private $inspector;

    /**
     * @param ProjectManager $projectManager
     * @param HostnameResolver $hostnameResolver
     * @param HostnameResolutionWriter $hostnameResolutionWriter
     * @param ContainerInspector $inspector
     */
    public function __construct(ProjectManager $projectManager, HostnameResolver $hostnameResolver, HostnameResolutionWriter $hostnameResolutionWriter, ContainerInspector $inspector)
    {
        $this->projectManager = $projectManager;
        $this->hostnameResolver = $hostnameResolver;
        $this->hostnameResolutionWriter = $hostnameResolutionWriter;
        $this->inspector = $inspector;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Project $project)
    {
        $hostnameConfigurations = $this->hostnameResolver->getExtraHostnameConfigurations($project);
        foreach ($hostnameConfigurations as $configuration) {
            $container = $this->inspector->findOneByName($configuration->getContainer());
            $ipAddress = $container->getIpAddress();

            if (empty($ipAddress)) {
                throw new \RuntimeException(sprintf(
                    'The container "%s" has an empty address',
                    $container->getName()
                ));
            }

            $this->hostnameResolutionWriter->write($configuration->getHostname(), $ipAddress);
        }

        $this->projectManager->start($project);

    }

    /**
     * {@inheritdoc}
     */
    public function stop(Project $project)
    {
        $this->projectManager->stop($project);
    }
}
