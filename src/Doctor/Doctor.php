<?php

namespace Dock\Doctor;

class Doctor
{
    /**
     * @var array
     */
    private $tasks;

    /**
     * @param array $tasks
     */
    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    public function examine()
    {
        foreach ($this->tasks as $task) {
            $task->run();
        }
    }
}
