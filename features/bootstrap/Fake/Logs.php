<?php

namespace Fake;

use Dock\IO\UserInteraction;

class Logs implements \Dock\Containers\Logs
{
    private $userInteraction;

    public function __construct(UserInteraction $userInteraction)
    {
        $this->userInteraction = $userInteraction;
    }

    public function displayAll()
    {
        $this->userInteraction->write('log line for all components');
    }

    /**
     * {@inheritdoc}
     */
    public function displayComponent($component)
    {
        $this->userInteraction->write('log line for component - '.$component);
    }
}