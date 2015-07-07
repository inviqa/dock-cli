<?php

namespace Dock\Cli;

use Dock\Cli\IO\ConsoleUserInteraction;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpCommand extends Command
{
    /** @var  ProcessRunner */
    private $processRunner;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('up')
            ->setDescription('Start the project')
        ;
    }

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userInteraction = new ConsoleUserInteraction($input, $output);
        $this->processRunner->setUserInteraction($userInteraction);

        if (!$this->inHomeDirectory()) {
            $output->writeln(
                '<error>The project have to be in your home directly to be able to share it with the Docker VM</error>'
            );

            return 1;
        }
        try {
            $dockerComposePath = $this->getDockerComposePath($this->processRunner);
            pcntl_exec($dockerComposePath, ['up']);

            return 0;
        } catch (ProcessFailedException $e) {
            return 1;
        }
    }

    private function inHomeDirectory()
    {
        $home = getenv('HOME');
        $pwd = getcwd();

        return substr($pwd, 0, strlen($home)) === $home;
    }

    private function getDockerComposePath(ProcessRunner $processRunner)
    {
        $output = $processRunner->run(new Process('which docker-compose'))->getOutput();

        return trim($output);
    }
}
