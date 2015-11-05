<?php

namespace Dock\Docker\Compose;

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
     * {@inheritdoc}
     */
    public function findAll()
    {
        $rawOutput = $this->processRunner->run('docker-compose ps -q')->getOutput();

        return $this->parseOutput($rawOutput);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        $rawOutput = $this->processRunner->run('docker-compose ps -q '.$name)->getOutput();
        $ids = $this->parseOutput($rawOutput);

        if (count($ids) == 0) {
            throw new \RuntimeException(sprintf(
                'No container named "%s" found',
                $name
            ));
        }

        return current($ids);
    }

    /**
     * @param string $output
     *
     * @return array
     */
    private function parseOutput($output)
    {
        $lines = explode("\n", $output);
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
