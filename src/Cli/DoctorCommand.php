<?php

namespace Dock\Cli;

use Dock\Doctor\Doctor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
            ->setDescription('Diagnose problems with Docker setup')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctor->examine();
    }
}
