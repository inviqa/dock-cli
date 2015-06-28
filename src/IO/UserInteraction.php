<?php

namespace Dock\IO;

use Symfony\Component\Console\Question\Question;

interface UserInteraction
{
    /**
     * @param string $string
     */
    public function writeTitle($string);

    /**
     * @param string $string
     */
    public function write($string);

    /**
     * @param Question $question
     *
     * @return string
     */
    public function ask(Question $question);
}
