<?php

namespace Dock\Cli\Docker;

use Dock\Doctor\Doctor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DoctorCommand extends Command
{
    /**
     * @var Doctor
     */
    private $doctor;

    /**
     * @param Doctor $doctor
     */
    public function __construct(Doctor $doctor)
    {
        parent::__construct();

        $this->doctor = $doctor;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:doctor')
            ->setDescription('Diagnose problems with Docker setup and attempt to fix them')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                "Diagnose problems, don't attempt to fix them"
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctor->examine($output, $input->getOption('dry-run'));
    }
}
