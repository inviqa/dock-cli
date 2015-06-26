<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\ChainProcessInterface;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerRouting extends IOTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(ConsoleContext $context)
    {
        $this->consoleHelper->writeTitle('Configure routing for direct Docker containers access');

        try {
            $process = new Process('sudo route -n add 172.17.0.0/16 192.168.59.103');
            $this->consoleHelper->runProcess($process, true);
        } catch (ProcessFailedException $e) {
            if (strpos($e->getProcess()->getErrorOutput(), 'File exists') !== false) {
                $this->consoleHelper->writeTitle('Routing already configured');
            } else {
                throw $e;
            }
        }

        // Add permanent routing
        if (!file_exists('/Library/LaunchDaemons/com.docker.route.plist')) {
            $source = __DIR__.'/fixtures/com.docker.route.plist';
            $dockerRouteFileContents = file_get_contents($source);
            $temporaryFile = tempnam(sys_get_temp_dir(), 'DockerInstaller');
            file_put_contents($temporaryFile, $dockerRouteFileContents);

            $process = new Process(sprintf('sudo cp %s /Library/LaunchDaemons/com.docker.route.plist', $temporaryFile));
            $this->consoleHelper->runProcess($process, true);

            $process = new Process('sudo launchctl load /Library/LaunchDaemons/com.docker.route.plist');
            $this->consoleHelper->runProcess($process, true);
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
