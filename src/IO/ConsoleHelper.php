<?php

namespace DockerInstaller\IO;

use DockerInstaller\ConsoleContext;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ConsoleHelper
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    private function __construct()
    {}

    /**
     * @param ConsoleContext $context
     * @return ConsoleHelper
     */
    public static function fromConsoleContext(ConsoleContext $context)
    {
        $self = new self();
        $self->input = $context->getInput();
        $self->output = $context->getOutput();

        return $self;
    }

    /**
     * @return callable
     */
    public function getRunningProcessCallback()
    {
        return function ($type, $buffer) {
            $lines = explode("\n", $buffer);
            $prefix = Process::ERR === $type ? '<error>ERR</error> ' : '<question>OUT</question> ';

            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $this->output->writeln($prefix.$line);
                }
            }
        };
    }

    /**
     * @param Process $process
     * @param bool $force
     * @return int|Process
     */
    public function runProcess(Process $process, $force = false)
    {
        $this->output->writeln('<info>RUN</info> '.$process->getCommandLine());

        if ($force) {
            return $process->mustRun($this->getRunningProcessCallback());
        }

        return $process->run($this->getRunningProcessCallback());
    }

    /**
     * @param string $name
     */
    public function writeTitle($name)
    {
        $formatter = new FormatterHelper();
        $formattedBlock = $formatter->formatBlock([$name], 'info');
        $this->output->writeln($formattedBlock);
    }
}
