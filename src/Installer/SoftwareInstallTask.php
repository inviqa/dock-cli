<?php

namespace Dock\Installer;

use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\NamedChainProcessInterface;

abstract class SoftwareInstallTask extends InstallerTask implements NamedChainProcessInterface
{
    /**
     * @var ProcessRunner
     */
    protected $processRunner;
    /**
     * @var UserInteraction
     */
    protected $userInteraction;

    /**
     * @param UserInteraction        $userInteraction
     * @param \Dock\IO\ProcessRunner $processRunner
     */
    public function __construct(UserInteraction $userInteraction, ProcessRunner $processRunner)
    {
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->processRunner->run($this->getVersionCommand(), false)->isSuccessful()) {
            $this->userInteraction->write(sprintf('"%s" is already installed', $this->getName()));
        } else {
            $this->userInteraction->write(sprintf('Installing "%s"', $this->getName()));
            $this->processRunner->run($this->getInstallCommand());
            $this->userInteraction->write(sprintf('"%s" successfully installed', $this->getName()));
        }
    }

    /**
     * @return string
     */
    abstract protected function getVersionCommand();

    /**
     * @return string
     */
    abstract protected function getInstallCommand();
}
