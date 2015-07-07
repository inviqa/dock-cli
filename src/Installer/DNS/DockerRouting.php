<?php

namespace Dock\Installer\DNS;

use Dock\Dinghy\DinghyCli;
use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use Dock\IO\ProcessRunner;
use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerRouting extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * @var DinghyCli
     */
    private $dinghy;

    /**
     * @param DinghyCli $dinghy
     */
    function __construct(DinghyCli $dinghy)
    {
        $this->dinghy = $dinghy;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $processRunner = $context->getProcessRunner();
        $userInteraction = $context->getUserInteraction();
        $userInteraction->writeTitle('Configure routing for direct Docker containers access');

        $dinghyIp = $this->dinghy->getIp();

        $this->configureRouting($dinghyIp, $processRunner, $userInteraction);
        $this->addPermanentRouting($dinghyIp, $processRunner);
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
     * @param ProcessRunner $processRunner
     * @param UserInteraction $userInteraction
     * @throws ProcessFailedException
     */
    private function configureRouting($dinghyIp, ProcessRunner $processRunner, UserInteraction $userInteraction)
    {
        try {
            $processRunner->run(sprintf('sudo route -n add 172.17.0.0/16 %s', $dinghyIp));
        } catch (ProcessFailedException $e) {
            if (strpos($e->getProcess()->getErrorOutput(), 'File exists') !== false) {
                $userInteraction->writeTitle('Routing already configured');

                return;
            }

            throw $e;
        }
    }

    /**
     * @param string $dinghyIp
     * @param ProcessRunner $processRunner
     */
    private function addPermanentRouting($dinghyIp, ProcessRunner $processRunner)
    {
        if (file_exists('/Library/LaunchDaemons/com.docker.route.plist')) {
            return;
        }

        $source = __DIR__ . '/fixtures/com.docker.route.plist';
        $dockerRouteFileContents = file_get_contents($source);
        $dockerRouteFileContents = str_replace('__DINGHY_IP__', $dinghyIp, $dockerRouteFileContents);

        $temporaryFile = tempnam(sys_get_temp_dir(), 'DockerInstaller');
        file_put_contents($temporaryFile, $dockerRouteFileContents);

        $processRunner->run(sprintf('sudo cp %s /Library/LaunchDaemons/com.docker.route.plist', $temporaryFile));
        $processRunner->run('sudo launchctl load /Library/LaunchDaemons/com.docker.route.plist');
    }
}
