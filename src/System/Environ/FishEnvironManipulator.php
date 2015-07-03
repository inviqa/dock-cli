<?php

namespace Dock\System\Environ;

use Dock\IO\ProcessRunner;
use Symfony\Component\Process\Process;

class FishEnvironManipulator implements EnvironManipulator
{
    /**
     * {@inheritdoc}
     */
    public function save(EnvironmentVariable $environmentVariable)
    {
        exec('set -g -x '.$environmentVariable->getName().' '.$environmentVariable->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public function has(EnvironmentVariable $environmentVariable)
    {
        $value = getenv($environmentVariable->getName());

        return !empty($value);
    }
}
