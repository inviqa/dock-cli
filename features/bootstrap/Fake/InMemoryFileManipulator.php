<?php

namespace Fake;

use Dock\IO\FileManipulator;

class InMemoryFileManipulator implements FileManipulator
{
    private $contents = null;

    /**
     * @param string $contents
     */
    public function __construct($contents)
    {
        $this->contents = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function write($contents)
    {
        $this->contents = $contents;
    }
}
