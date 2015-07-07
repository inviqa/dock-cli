<?php

namespace Dock\Installer;

use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainContext;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class InstallContext implements ChainContext, ProcessRunner, UserInteraction
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param ProcessRunner $processRunner
     * @param UserInteraction $userInteraction
     */
    public function __construct(ProcessRunner $processRunner, UserInteraction $userInteraction)
    {
        $this->processRunner = $processRunner;
        $this->userInteraction = $userInteraction;
    }

    /**
     * @param string $command
     * @param bool $mustSucceed
     * @return Process
     */
    public function run($command, $mustSucceed = true)
    {
        return $this->processRunner->run($command, $mustSucceed);
    }

    /**
     * @param string $string
     */
    public function writeTitle($string)
    {
        $this->userInteraction->writeTitle($string);
    }

    /**
     * @param string $string
     */
    public function write($string)
    {
        $this->userInteraction->write($string);
    }

    /**
     * @param Question $question
     *
     * @return string
     */
    public function ask(Question $question)
    {
        return $this->userInteraction->ask($question);
    }

    /**
     * @return \Dock\IO\ProcessRunner
     */
    public function getProcessRunner()
    {
        return $this->processRunner;
    }
}
