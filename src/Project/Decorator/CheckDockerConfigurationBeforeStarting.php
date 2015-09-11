<?php

namespace Dock\Project\Decorator;

use Dock\Doctor\CommandFailedException;
use Dock\Doctor\Doctor;
use Dock\IO\UserInteraction;
use Dock\Project\ProjectManager;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Question\Question;

class CheckDockerConfigurationBeforeStarting implements ProjectManager
{
    /**
     * @var ProjectManager
     */
    private $projectManager;

    /**
     * @var Doctor
     */
    private $doctor;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param ProjectManager $projectManager
     * @param Doctor $doctor
     * @param UserInteraction $userInteraction
     */
    public function __construct(ProjectManager $projectManager, Doctor $doctor, UserInteraction $userInteraction)
    {
        $this->projectManager = $projectManager;
        $this->doctor = $doctor;
        $this->userInteraction = $userInteraction;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        try {
            $this->doctor->examine(new NullOutput(), true);
        } catch (CommandFailedException $e) {
            $answer = $this->userInteraction->ask(new Question(
                'It looks like there\'s something wrong with your installation. Would you like to run the `doctor` command ? [Yn]',
                'y'
            ));

            if ('y' == strtolower($answer)) {
                $this->doctor->examine($this->userInteraction, false);
            }
        }

        return $this->projectManager->start();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        return $this->projectManager->stop();
    }
}
