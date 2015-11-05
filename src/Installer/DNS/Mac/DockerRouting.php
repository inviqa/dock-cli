<?php

namespace Dock\Installer\DNS\Mac;

use Dock\Dinghy\DinghyCli;
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
     * @var DinghyCli
     */
    private $dinghy;
    /**
     * @var PharFileExtractor
     */
    private $fileExtractor;

    /**
     * @param DinghyCli         $dinghy
     * @param UserInteraction   $userInteraction
     * @param ProcessRunner     $processRunner
     * @param PharFileExtractor $fileExtractor
     */
    public function __construct(DinghyCli $dinghy, UserInteraction $userInteraction, ProcessRunner $processRunner, PharFileExtractor $fileExtractor)
    {
        $this->dinghy = $dinghy;
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
     *
     * @throws ProcessFailedException
     */
    private function configureRouting($dinghyIp)
    {
        $this->processRunner->run('sudo route -n delete 172.17.0.0/16', false);
        $this->processRunner->run(sprintf('sudo route -n add 172.17.0.0/16 %s', $dinghyIp));
    }

    /**
     * @param string $dinghyIp
     */
    private function addPermanentRouting($dinghyIp)
    {
        $filePath = $this->fileExtractor->extract(__DIR__.'/fixtures/com.docker.route.plist');

        // Replace the Dinghy IP
        file_put_contents($filePath, str_replace('__DINGHY_IP__', $dinghyIp, file_get_contents($filePath)));

        // Replace the network interface used
        $dinghyInterface = $this->resolveDinghyNetworkInterface($dinghyIp);
        file_put_contents($filePath, str_replace('__DINGHY_INTERFACE__', $dinghyInterface, file_get_contents($filePath)));

        $this->processRunner->run(sprintf('sudo cp %s /Library/LaunchDaemons/com.docker.route.plist', $filePath));
        $this->processRunner->run('sudo launchctl load /Library/LaunchDaemons/com.docker.route.plist');
    }

    /**
     * Resolve the network interface name for Dinghy IP.
     *
     * @param string $dinghyIp
     *
     * @return string
     */
    private function resolveDinghyNetworkInterface($dinghyIp)
    {
        $process = $this->processRunner->run('ifconfig `route get '.$dinghyIp.' | grep "interface: " | sed "s/[^:]*: \(.*\)/\1/"` | head -n 1 | sed "s/\([^:]*\): .*/\1/"');
        $interface = $process->getOutput();

        return trim($interface);
    }
}
