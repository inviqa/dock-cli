<?php

namespace Dock\Docker;

use Dock\Containers\Container;
use Dock\Docker\Dns\ContainerAddressResolver;
use Dock\IO\ProcessRunner;

class ContainerDetails implements \Dock\Containers\ContainerDetails
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var ContainerAddressResolver
     */
    private $containerAddressResolver;

    /**
     * @param ProcessRunner $processRunner
     * @param ContainerAddressResolver $containerAddressResolver
     */
    public function __construct(ProcessRunner $processRunner, ContainerAddressResolver $containerAddressResolver)
    {
        $this->processRunner = $processRunner;
        $this->containerAddressResolver = $containerAddressResolver;
    }

    /**
     * @param string $containerId
     * @return Container
     */
    public function findById($containerId)
    {
        return $this->getContainerFromInspection($this->inspectContainer($containerId));
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
        $ipAddress = isset($inspection['NetworkSettings']['IPAddress']) ? $inspection['NetworkSettings']['IPAddress'] : null;

        return new Container(
            $containerName,
            $imageName,
            $inspection['State']['Running'] ? Container::STATE_RUNNING : Container::STATE_EXITED,
            $this->containerAddressResolver->getDnsByContainerNameAndImage($containerName, $imageName),
            $exposedPorts,
            $componentName,
            $ipAddress
        );
    }
}
