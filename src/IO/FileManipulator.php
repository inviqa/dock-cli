<?php

namespace Dock\IO;

interface FileManipulator
{
    /**
     * Read the contents from the file.
     *
     * @return string
     */
    public function read();

    /**
     * @param string $contents
     *
     * @return bool
     */
    public function write($contents);
}
