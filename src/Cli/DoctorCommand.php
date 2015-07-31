<?php

namespace Dock\Cli;

use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DoctorCommand extends Command
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        parent::__construct();

        $this->processRunner = $processRunner;
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
        $this->verifyDockerUpAndRunning();
        $this->verifyDnsDock($output);
    }

    private function verifyDockerUpAndRunning()
    {
        try {
            $this->processRunner->run('docker -v');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `docker -v` failed - it seems docker is not installed.\n"
                . "Install it with `dock-cli docker:install`");
        }

        try {
            $this->processRunner->run('docker info');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `docker info` failed - it seems the docker daemon is not running.\n"
                . "Start it with `sudo service docker start`");
        }
    }

    private function verifyDnsDock()
    {
        $dnsdockId = $this->processRunner->run('docker ps -q --filter=name=dnsdock')->getOutput();

        if ($dnsdockId === null) {
            throw new \Exception("Command `docker ps -q --filter=name=dnsdock` did not return any results - seems dnsdock is not running.\n"
                . "Start it with `dock-cli docker:install`");
        }

        try {
            $this->processRunner->run('ping -c1 172.17.42.1');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `ping -c1 172.17.42.1` failed - we can't reach the dnsdock container.\n"
                . "TODO? This should never happen.");
        }

        try {
            $this->processRunner->run('ping -c1 dnsdock.docker');
        } catch (ProcessFailedException $e) {
            throw new \Exception("Command `ping -c1 dnsdock.docker` failed - it seems your dns is not set up properly.\n"
                . "Add 172.17.42.1 as one of your DNS servers. `dock-cli docker:install` will try to do that");
        }
    }
}
