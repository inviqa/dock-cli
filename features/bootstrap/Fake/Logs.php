<?php

namespace Fake;

use Dock\IO\UserInteraction;

class Logs implements \Dock\Containers\Logs
{
    private $userInteraction;
    private $runningContainerIds = [];

    public function __construct(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }

    public function setRunningContainerIds(array $containerIds)
    {
        $this->runningContainerIds = $containerIds;
    }

    public function displayAll()
    {
        $this->displayLogs($this->runningContainerIds);
    }

    /**
     * {@inheritdoc}
     */
    public function displayComponent($component)
    {
        $this->displayLogs(
            array_filter(
                $this->runningContainerIds,
                function ($id) use ($component) {
                    return $id == $component;
                }
            )
        );
    }

    /**
     * @param $ids
     */
    private function displayLogs($ids)
    {
        foreach ($ids as $id) {
            $this->userInteraction->write("[$id] is running");
        }
    }
}
