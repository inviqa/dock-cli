<?php

namespace Dock\Cli\IO;

use Dock\IO\UserInteraction;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConsoleUserInteraction implements UserInteraction
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * This method is called when a console command event is fired.
     *
     * @param ConsoleCommandEvent $event
     */
    public function onCommand(ConsoleCommandEvent $event)
    {
        $this->input = $event->getInput();
        $this->output = $event->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function writeTitle($name)
    {
        $formatter = new FormatterHelper();
        $formattedBlock = $formatter->formatBlock([$name], 'info');
        $this->getOutput()->writeln($formattedBlock);
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $this->getOutput()->writeln($string);
    }

    /**
     * {@inheritdoc}
     */
    public function ask(Question $question)
    {
        $questionHelper = new QuestionHelper();

        return $questionHelper->ask($this->getInput(), $this->getOutput(), $question);
    }

    /**
     * @throws \RuntimeException
     *
     * @return InputInterface
     */
    private function getInput()
    {
        if (null === $this->input) {
            throw new \RuntimeException('No user interaction context available.');
        }

        return $this->input;
    }

    /**
     * @throws \RuntimeException
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        if (null === $this->output) {
            throw new \RuntimeException('No user interaction context available.');
        }

        return $this->output;
    }
}
