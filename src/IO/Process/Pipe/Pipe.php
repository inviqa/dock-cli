<?php

namespace Dock\IO\Process\Pipe;

interface Pipe
{
    /**
     * Pipe this error buffer.
     *
     * @param string $buffer
     */
    public function error($buffer);

    /**
     * Pipe this output buffer.
     *
     * @param string $buffer
     */
    public function output($buffer);
}
