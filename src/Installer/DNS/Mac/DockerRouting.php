<?php

namespace Dock\Installer\DNS\Mac;

use Dock\Dinghy\DinghyCli;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerRouting extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;
    /**
     * @var UserInteraction
     */
    private $userInteraction;
    /**
     * @var DinghyCli
     */
    private $dinghy;

    /**
     * @param DinghyCli $dinghy
     * @param UserInteraction $userInteraction
     * @param ProcessRunner $processRunner
     */
    public function __construct(DinghyCli $dinghy, UserInteraction $userInteraction, ProcessRunner $processRunner)
    {
        $this->dinghy = $dinghy;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->userInteraction->writeTitle('Configure routing for direct Docker containers access');

        $dinghyIp = $this->dinghy->getIp();

        $this->configureRouting($dinghyIp);
        $this->addPermanentRouting($dinghyIp);
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['dinghy'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routing';
    }

    /**
     * @param string $dinghyIp
     * @throws ProcessFailedException
     */
    private function configureRouting($dinghyIp)
    {
        try {
            $this->processRunner->run(sprintf('sudo route -n add 172.17.0.0/16 %s', $dinghyIp));
        } catch (ProcessFailedException $e) {
            if (strpos($e->getProcess()->getErrorOutput(), 'File exists') !== false) {
                $this->userInteraction->writeTitle('Routing already configured');

                return;
            }

            throw $e;
        }
    }

    /**
     * @param string $dinghyIp
     */
    private function addPermanentRouting($dinghyIp)
    {
        if (file_exists('/Library/LaunchDaemons/com.docker.route.plist')) {
            return;
        }

        $source = __DIR__.'/fixtures/com.docker.route.plist';
        $dockerRouteFileContents = file_get_contents($source);
        $dockerRouteFileContents = str_replace('__DINGHY_IP__', $dinghyIp, $dockerRouteFileContents);

        $temporaryFile = tempnam(sys_get_temp_dir(), 'DockerInstaller');
        file_put_contents($temporaryFile, $dockerRouteFileContents);

        $this->processRunner->run(sprintf('sudo cp %s /Library/LaunchDaemons/com.docker.route.plist', $temporaryFile));
        $this->processRunner->run('sudo launchctl load /Library/LaunchDaemons/com.docker.route.plist');
    }
}
