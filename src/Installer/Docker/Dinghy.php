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
     * @param Boot2DockerCli  $boot2docker
     * @param DinghyCli       $dinghy
     * @param UserInteraction $userInteraction
     * @param ProcessRunner   $processRunner
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

    /**
     * Update the default DNS resolver namespace of Dinghy.
     */
    private function changeDinghyDnsResolverNamespace()
    {
        $dnsMasqConfigurationPath = $this->getDinghyDnsMasqFilePath();
        $dinghyDnsMasqContents = file_get_contents($dnsMasqConfigurationPath);
        $dinghyDnsMasqContents = preg_replace('#RESOLVER_FILE = RESOLVER_DIR\.join\("([a-z-]+)"\)#', 'RESOLVER_FILE = RESOLVER_DIR.join("zzz-dinghy2")', $dinghyDnsMasqContents);
        file_put_contents($dnsMasqConfigurationPath, $dinghyDnsMasqContents);
    }

    /**
     * @return string
     */
    private function getDinghyDnsMasqFilePath()
    {
        $versions = [$this->dinghy->getVersion(), 'HEAD'];

        foreach ($versions as $version) {
            $path = '/usr/local/Cellar/dinghy/'.$version.'/cli/dinghy/dnsmasq.rb';

            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException('Unable to find Dinghy\'s dnsmask configuration file');
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
        if (!$this->boot2docker->isInstalled()) {
            return;
        }

        $this->userInteraction->writeTitle('Boot2Docker seems to be installed, removing it.');

        if (!$this->boot2docker->uninstall()) {
            $this->userInteraction->writeTitle(
                'Something went wrong while uninstalling Boot2Docker, continuing anyway.'
            );

            return;
        }
        $this->userInteraction->writeTitle('Successfully uninstalled boot2docker');
    }

    private function installDinghy()
    {
        if ($this->dinghy->isInstalled()) {
            $version = $this->dinghy->getVersion();

            if (version_compare($version, '4.0.0') >= 0) {
                $this->userInteraction->writeTitle('Dinghy already installed, skipping.');

                return;
            } else {
                $this->userInteraction->writeTitle('And old Dinghy version found, upgrading.');

                $this->upgradeDinghy();
            }
        } else {
            $this->userInteraction->writeTitle('Installing Dinghy');
            $this->processRunner->run(
                'brew install https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb'
            );
        }

        $this->userInteraction->writeTitle('Successfully installed Dinghy');
    }

    private function startDinghy()
    {
        if ($this->dinghy->isRunning()) {
            $this->userInteraction->writeTitle('Dinghy already started');

            return;
        }

        $this->userInteraction->writeTitle('Starting up Dinghy');
        if (!$this->dinghy->isCreated()) {
            $this->dinghy->create();
        } else {
            $this->dinghy->start();
        }

        $this->userInteraction->writeTitle('Started Dinghy');
    }

    private function upgradeDinghy()
    {
        $this->processRunner->run(
            'brew reinstall --HEAD https://github.com/codekitchen/dinghy/raw/latest/dinghy.rb'
        );
    }
}
