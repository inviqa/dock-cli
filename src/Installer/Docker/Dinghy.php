<?php

namespace Dock\Installer\Docker;

use Dock\Dinghy\Boot2DockerCli;
use Dock\Dinghy\DinghyCli;
use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;

class Dinghy extends InstallerTask implements DependentChainProcessInterface
{
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
     */
    public function __construct(Boot2DockerCli $boot2docker, DinghyCli $dinghy)
    {
        $this->boot2docker = $boot2docker;
        $this->dinghy = $dinghy;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $this->uninstallBoot2Docker($context);
        $this->installDinghy($context);
        $this->changeDinghyDnsResolverNamespace($context);
        $this->startDinghy($context);
    }

    private function changeDinghyDnsResolverNamespace(InstallContext $context)
    {
        $dinghyVersionOutput = $context->run('dinghy version')->getOutput();
        $dinghyVersion = substr(trim($dinghyVersionOutput), strlen('Dinghy '));
        $dnsMasqConfiguration = '/usr/local/Cellar/dinghy/'.$dinghyVersion.'/cli/dinghy/dnsmasq.rb';

        $process = 'sed -i \'\' \'s/docker/zzz-dinghy/\' '.$dnsMasqConfiguration;
        $context->run($process);
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

    private function uninstallBoot2Docker(InstallContext $context)
    {
        if ($this->boot2docker->isInstalled()) {
            $context->writeTitle('Boot2Docker seems to be installed, removing it.');

            if (!$this->boot2docker->uninstall()) {
                $context->writeTitle(
                    'Something went wrong while uninstalling Boot2Docker, continuing anyway.'
                );
            } else {
                $context->writeTitle('Successfully uninstalled boot2docker');
            }
        }
    }

    private function installDinghy(InstallContext $context)
    {
        if ($this->dinghy->isInstalled()) {
            $context->writeTitle('Dinghy already installed, skipping.');

            return;
        }

        $context->writeTitle('Installing Dinghy');
        $context->run(
            'brew install https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb'
        );
        $context->writeTitle('Successfully installed Dinghy');
    }

    private function startDinghy(InstallContext $context)
    {
        $context->writeTitle('Starting up Dinghy');

        if ($this->dinghy->isRunning()) {
            $context->writeTitle('Dinghy already started');

            return;
        }

        $this->dinghy->start();
        $context->writeTitle('Started Dinghy');
    }
}
