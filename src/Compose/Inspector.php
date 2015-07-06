<?php

namespace Dock\Compose;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Process;

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
            $containerName = substr($inspection['Name'], 1);
            $imageName = $inspection['Config']['Image'];
            $exposedPorts = isset($inspection['Config']['ExposedPorts']) ? array_keys($inspection['Config']['ExposedPorts']) : [];

            $containers[] = new Container(
                $containerName,
                $imageName,
                $inspection['State']['Running'] ? Container::STATE_RUNNING : Container::STATE_EXITED,
                $this->getDnsByContainerNameAndImage($containerName, $imageName),
                $exposedPorts
            );
        }

        return $containers;
    }

    /**
     * @param string $containerName
     * @param string $containerImage
     * @return array
     */
    private function getDnsByContainerNameAndImage($containerName, $containerImage)
    {
        return [
            $containerImage.'.docker',
            $containerName.'.'.$containerImage.'.docker'
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
        $rawOutput = $this->processRunner->run(new Process($command))->getOutput();
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
        $rawOutput = $this->processRunner->run(new Process('docker-compose ps -q'))->getOutput();
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
