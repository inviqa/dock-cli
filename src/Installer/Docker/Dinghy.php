<?php

namespace Dock\Installer\Docker;

use Dock\Dinghy\Boot2DockerCli;
use Dock\Dinghy\DinghyCli;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class Dinghy extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var UserInteraction
     */
    private $userInteraction;
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var Boot2DockerCli
     */
    private $boot2docker;

    /**
     * @var DinghyCli
     */
    private $dinghy;

    /**
     * @param Boot2DockerCli $boot2docker
     * @param DinghyCli $dinghy
     * @param UserInteraction $userInteraction
     * @param ProcessRunner $processRunner
     */
    public function __construct(
        Boot2DockerCli $boot2docker,
        DinghyCli $dinghy,
        UserInteraction $userInteraction,
        ProcessRunner $processRunner
    ) {
        $this->boot2docker = $boot2docker;
        $this->dinghy = $dinghy;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->uninstallBoot2Docker();
        $this->installDinghy();
        $this->changeDinghyDnsResolverNamespace();
        $this->startDinghy();
    }

    private function changeDinghyDnsResolverNamespace()
    {
        $process = $this->processRunner->run('dinghy version');
        $dinghyVersionOutput = $process->getOutput();
        $dinghyVersion = substr(trim($dinghyVersionOutput), strlen('Dinghy '));
        $dnsMasqConfiguration = '/usr/local/Cellar/dinghy/'.$dinghyVersion.'/cli/dinghy/dnsmasq.rb';

        $process = 'sed -i \'\' \'s/docker/zzz-dinghy/\' '.$dnsMasqConfiguration;
        $this->processRunner->run($process);
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['homebrew', 'vagrant', 'virtualbox'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dinghy';
    }

    private function uninstallBoot2Docker()
    {
        if ($this->boot2docker->isInstalled()) {
            $this->userInteraction->writeTitle('Boot2Docker seems to be installed, removing it.');

            if (!$this->boot2docker->uninstall()) {
                $this->userInteraction->writeTitle(
                    'Something went wrong while uninstalling Boot2Docker, continuing anyway.'
                );
            } else {
                $this->userInteraction->writeTitle('Successfully uninstalled boot2docker');
            }
        }
    }

    private function installDinghy()
    {
        if ($this->dinghy->isInstalled()) {
            $this->userInteraction->writeTitle('Dinghy already installed, skipping.');

            return;
        }

        $this->userInteraction->writeTitle('Installing Dinghy');
        $this->processRunner->run(
            'brew install https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb'
        );
        $this->userInteraction->writeTitle('Successfully installed Dinghy');
    }

    private function startDinghy()
    {
        $this->userInteraction->writeTitle('Starting up Dinghy');

        if ($this->dinghy->isRunning()) {
            $this->userInteraction->writeTitle('Dinghy already started');

            return;
        }

        $this->dinghy->start();
        $this->userInteraction->writeTitle('Started Dinghy');
    }
}
