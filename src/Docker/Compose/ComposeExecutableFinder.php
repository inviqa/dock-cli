<?php

namespace Dock\Docker\Compose;

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
            throw new \RuntimeException(
                'Unable to find docker-compose binary. '.
                'You should run `docker:install` to set up your Docker environment'
            );
        }

        return $executable;
    }
}
