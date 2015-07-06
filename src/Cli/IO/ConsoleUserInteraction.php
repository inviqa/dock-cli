<?php

namespace Dock\Cli\IO;

use Dock\IO\UserInteraction;
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;
    }

    /**
     * {@inheritdoc}
     */
    public function writeTitle($name)
    {
        $formatter = new FormatterHelper();
        $formattedBlock = $formatter->formatBlock([$name], 'info');
        $this->output->writeln($formattedBlock);
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $this->output->writeln($string);
    }

    /**
     * {@inheritdoc}
     */
    public function ask(Question $question)
    {
        $questionHelper = new QuestionHelper();

        return $questionHelper->ask($this->input, $this->output, $question);
    }
}
