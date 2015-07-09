<?php

namespace Dock\DockerCompose;

use Dock\IO\ProcessRunner;

class ConfiguredContainerIds implements \Dock\Containers\ConfiguredContainerIds
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return array
     */
    public function findAll()
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
