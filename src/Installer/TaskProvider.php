<?php

namespace Dock\Installer;

use SRIO\ChainOfResponsibility\ChainBuilder;

class TaskProvider
{
    private $tasks;

    /**
     * @param array $tasks
     */
    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return ChainBuilder
     */
    public function getTasks()
    {
        return new ChainBuilder($this->tasks);
    }
}
