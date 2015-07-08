<?php

namespace Dock\IO\Process\Pipe;

class NullPipe implements Pipe
{
    /**
     * {@inheritdoc}
     */
    public function error($buffer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function output($buffer)
    {
    }
}
