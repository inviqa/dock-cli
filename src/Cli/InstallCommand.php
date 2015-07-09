<?php

namespace Dock\Cli;

use Dock\Installer\DockerInstaller;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var DockerInstaller
     */
    private $dockerInstaller;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param DockerInstaller $dockerInstaller
     * @param ProcessRunner $processRunner
     */
    public function __construct(DockerInstaller $dockerInstaller, ProcessRunner $processRunner)
    {
        parent::__construct();

        $this->dockerInstaller = $dockerInstaller;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('docker:install')
            ->setDescription('Install Docker on OSX')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dockerInstaller->install();
        $this->processRunner->followsUpWith(getenv('SHELL'));
    }
}
