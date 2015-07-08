<?php

namespace Dock\Compose;

use Symfony\Component\Process\ExecutableFinder;

class ComposeExecutableFinder
{
    /**
     * @var ExecutableFinder
     */
    private $executableFinder;

    public function __construct()
    {
        $this->executableFinder = new ExecutableFinder();
    }

    /**
     * @return string
     */
    public function find()
    {
        if (null === ($executable = $this->executableFinder->find('docker-compose'))) {
            throw new \RuntimeException('Unable to find docker-compose binary');
        }

        return $executable;
    }
}
