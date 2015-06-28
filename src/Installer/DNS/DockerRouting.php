<?php

namespace Dock\Installer\DNS;

use Dock\Dinghy\DinghyCli;
use Dock\Installer\InstallContext;
use Dock\Installer\InstallerTask;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerRouting extends InstallerTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(InstallContext $context)
    {
        $processRunner = $context->getProcessRunner();
        $userInteraction = $context->getUserInteraction();
        $userInteraction->writeTitle('Configure routing for direct Docker containers access');

        $dinghy = new DinghyCli($processRunner);
        $dinghyIp = $dinghy->getIp();

        try {
            $process = new Process(sprintf('sudo route -n add 172.17.0.0/16 %s', $dinghyIp));
            $processRunner->run($process);
        } catch (ProcessFailedException $e) {
            if (strpos($e->getProcess()->getErrorOutput(), 'File exists') !== false) {
                $userInteraction->writeTitle('Routing already configured');
            } else {
                throw $e;
            }
        }

        // Add permanent routing
        if (!file_exists('/Library/LaunchDaemons/com.docker.route.plist')) {
            $source = __DIR__.'/fixtures/com.docker.route.plist';
            $dockerRouteFileContents = file_get_contents($source);
            $dockerRouteFileContents = str_replace('__DINGLY_IP__', $dinghyIp, $dockerRouteFileContents);

            $temporaryFile = tempnam(sys_get_temp_dir(), 'DockerInstaller');
            file_put_contents($temporaryFile, $dockerRouteFileContents);

            $processRunner->run(new Process(sprintf(
                'sudo cp %s /Library/LaunchDaemons/com.docker.route.plist',
                $temporaryFile
            )));

            $processRunner->run(new Process(
                'sudo launchctl load /Library/LaunchDaemons/com.docker.route.plist'
            ));
        }
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
}
