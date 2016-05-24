<?php

namespace Dock\System\Environ;

class FileEnvironManipulator implements EnvironManipulator
{
    /**
     * @var string
     */
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function save($declaration)
    {
        file_put_contents($this->file, PHP_EOL.$declaration, FILE_APPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function has($declaration)
    {
        return is_file($this->file) && false !== strpos(file_get_contents($this->file), $declaration);
    }
}
