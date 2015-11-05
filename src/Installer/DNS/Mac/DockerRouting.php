<?php

namespace Dock\Installer\DNS\Mac;

use Dock\Docker\Machine\Machine;
use Dock\Installer\InstallerTask;
use Dock\IO\PharFileExtractor;
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
     * @var PharFileExtractor
     */
    private $fileExtractor;
    /**
     * @var Machine
     */
    private $machine;

    /**
     * @param Machine           $machine
     * @param UserInteraction   $userInteraction
     * @param ProcessRunner     $processRunner
     * @param PharFileExtractor $fileExtractor
     */
    public function __construct(Machine $machine, UserInteraction $userInteraction, ProcessRunner $processRunner, PharFileExtractor $fileExtractor)
    {
        $this->machine = $machine;
        $this->userInteraction = $userInteraction;
        $this->processRunner = $processRunner;
        $this->fileExtractor = $fileExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->userInteraction->writeTitle('Configure routing for direct Docker containers access');

        $machineIp = $this->machine->getIp();

        $this->configureRouting($machineIp);
        $this->addPermanentRouting($machineIp);
    }

    /**
     * {@inheritdoc}
     */
    public function dependsOn()
    {
        return ['dockerMachine'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routing';
    }

    /**
     * @param string $machineIp
     *
     * @throws ProcessFailedException
     */
    private function configureRouting($machineIp)
    {
        $this->processRunner->run('sudo route -n delete 172.17.0.0/16', false);
        $this->processRunner->run(sprintf('sudo route -n add 172.17.0.0/16 %s', $machineIp));
    }

    /**
     * @param string $machineIp
     */
    private function addPermanentRouting($machineIp)
    {
        $filePath = $this->fileExtractor->extract(__DIR__.'/fixtures/com.docker.route.plist');

        // Replace the Dinghy IP
        file_put_contents($filePath, str_replace('__MACHINE_IP__', $machineIp, file_get_contents($filePath)));

        // Replace the network interface used
        $machineInterface = $this->resolveNetworkInterface($machineIp);
        file_put_contents($filePath, str_replace('__MACHINE_INTERFACE__', $machineInterface, file_get_contents($filePath)));

        $this->processRunner->run(sprintf('sudo cp %s /Library/LaunchDaemons/com.docker.route.plist', $filePath));
        $this->processRunner->run('sudo launchctl load /Library/LaunchDaemons/com.docker.route.plist');
    }

    /**
     * Resolve the network interface name for the given IP.
     *
     * @param string $machineIp
     *
     * @return string
     */
    private function resolveNetworkInterface($machineIp)
    {
        $process = $this->processRunner->run('ifconfig `route get '.$machineIp.' | grep "interface: " | sed "s/[^:]*: \(.*\)/\1/"` | head -n 1 | sed "s/\([^:]*\): .*/\1/"');
        $interface = $process->getOutput();

        return trim($interface);
    }
}
