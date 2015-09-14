<?php

namespace Dock\Doctor;

use Symfony\Component\Console\Output\OutputInterface;

class TaskBasedDoctor implements Doctor
{
    /**
     * @var Task[]
     */
    private $tasks;

    /**
     * @param Task[] $tasks
     */
    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * {@inheritdoc}
     */
    public function examine(OutputInterface $output, $dryRun)
    {
        $output->writeLn('<info>Running Docker Doctor</info>');

        foreach ($this->tasks as $task) {
            $task->run($output, $dryRun);
        }

        $output->writeLn('<info>Yay! Your setup is working perfectly!</info>');
    }
}
