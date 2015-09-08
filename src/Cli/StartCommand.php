<?php

namespace Dock\Cli;

use Dock\Doctor\CommandFailedException;
use Dock\Doctor\Doctor;
use Dock\IO\Process\InteractiveProcessBuilder;
use Dock\IO\Process\InteractiveProcessManager;
use Dock\IO\UserInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StartCommand extends Command
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @var InteractiveProcessBuilder
     */
    private $interactiveProcessBuilder;

    /**
     * @var Doctor
     */
    private $doctor;

    /**
     * @param InteractiveProcessBuilder $interactiveProcessBuilder
     * @param UserInteraction $userInteraction
     * @param Doctor $doctor
     */
    public function __construct(InteractiveProcessBuilder $interactiveProcessBuilder, UserInteraction $userInteraction, Doctor $doctor)
    {
        parent::__construct();

        $this->userInteraction = $userInteraction;
        $this->interactiveProcessBuilder = $interactiveProcessBuilder;
        $this->doctor = $doctor;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Start the project')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->inHomeDirectory()) {
            $output->writeln(
                '<error>The project have to be in your home directly to be able to share it with the Docker VM</error>'
            );

            return 1;
        }

        try {
            $this->doctor->examine(new NullOutput(), true);
        } catch (CommandFailedException $e) {
            $answer = $this->userInteraction->ask(new Question('It looks like there\'s something wrong with your installation. Would you like to run the `doctor` command ? [Yn]', 'y'));

            if ('y' == strtolower($answer)) {
                $this->doctor->examine($output, false);
            }
        }

        $this->userInteraction->writeTitle('Starting application containers');

        $this->interactiveProcessBuilder
            ->forCommand('docker-compose up -d')
            ->disableOutput()
            ->withoutTimeout()
            ->ifTakesMoreThan(5000, function (InteractiveProcessManager $processManager) {
                $processManager->enableOutput(true);
            })
            ->getManager()
            ->run();
        ;

        return $this->getApplication()->run(
            new ArrayInput(['command' => 'ps']),
            $output
        );
    }

    /**
     * @return bool
     */
    private function inHomeDirectory()
    {
        $home = getenv('HOME');
        $pwd = getcwd();

        return substr($pwd, 0, strlen($home)) === $home;
    }
}
