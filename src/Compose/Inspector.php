<?php

namespace Dock\Compose;

use Dock\IO\ProcessRunner;

class Inspector
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return Container[]
     */
    public function getRunningContainers()
    {
        $containerIds = $this->getRunningContainerIds();
        $containers = [];

        foreach ($containerIds as $containerId) {
            $inspection = $this->inspectContainer($containerId);

            $containers[] = $this->getContainerFromInspection($inspection);
        }

        return $containers;
    }

    /**
     * @param array $inspection
     *
     * @return Container
     */
    private function getContainerFromInspection(array $inspection)
    {
        $containerName = substr($inspection['Name'], 1);
        $containerConfiguration = $inspection['Config'];
        $imageName = $containerConfiguration['Image'];
        $exposedPorts = isset($containerConfiguration['ExposedPorts']) ? array_keys(
            $containerConfiguration['ExposedPorts']
        ) : [];
        $componentName = isset($containerConfiguration['Labels']['com.docker.compose.service']) ? $containerConfiguration['Labels']['com.docker.compose.service'] : null;

        return new Container(
            $containerName,
            $imageName,
            $inspection['State']['Running'] ? Container::STATE_RUNNING : Container::STATE_EXITED,
            $this->getDnsByContainerNameAndImage($containerName, $imageName),
            $exposedPorts,
            $componentName
        );
    }

    /**
     * @param string $containerName
     * @param string $containerImage
     *
     * @return array
     */
    private function getDnsByContainerNameAndImage($containerName, $containerImage)
    {
        return [
            $containerImage.'.docker',
            $containerName.'.'.$containerImage.'.docker',
        ];
    }

    /**
     * @param string $containerId
     *
     * @return array
     */
    private function inspectContainer($containerId)
    {
        $command = sprintf('docker inspect %s', $containerId);
        $rawOutput = $this->processRunner->run($command)->getOutput();
        $rawOutput = trim($rawOutput);

        if (null === ($inspection = json_decode($rawOutput, true))) {
            throw new \RuntimeException('Unable to inspect container');
        }

        return $inspection[0];
    }

    /**
     * @return array
     */
    private function getRunningContainerIds()
    {
        $rawOutput = $this->processRunner->run('docker-compose ps -q')->getOutput();
        $lines = explode("\n", $rawOutput);
        $containerIds = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $containerIds[] = $line;
            }
        }

        return $containerIds;
    }
}
