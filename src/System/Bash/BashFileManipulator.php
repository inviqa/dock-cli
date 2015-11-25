<?php

namespace Dock\System\Bash;

use Dock\IO\FileManipulator;

class BashFileManipulator
{
    /**
     * @var FileManipulator
     */
    private $fileManipulator;

    /**
     * @param FileManipulator $fileManipulator
     */
    public function __construct(FileManipulator $fileManipulator)
    {
        $this->fileManipulator = $fileManipulator;
    }

    /**
     * @param string $variableName
     *
     * @return string|null
     */
    public function getValue($variableName)
    {
        if (preg_match($this->getVariablePattern($variableName), $this->fileManipulator->read(), $matches)) {
            return trim($matches[1]);
        }

        return;
    }

    /**
     * @param string $variableName
     * @param string $value
     */
    public function replaceValue($variableName, $value)
    {
        $variableDeclaration = $variableName.'=\''.$value.'\'';
        if (null === $this->getValue($variableName)) {
            $contents = $this->fileManipulator->read().PHP_EOL.$variableDeclaration;
        } else {
            $contents = preg_replace($this->getVariablePattern($variableName), $variableDeclaration, $this->fileManipulator->read());
        }

        $this->fileManipulator->write($contents);
    }

    /**
     * @param string $variableName
     *
     * @return string
     */
    private function getVariablePattern($variableName)
    {
        return '/'.$variableName.'=\'?([^\']*)\'?/mi';
    }
}
