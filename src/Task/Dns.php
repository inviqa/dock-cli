<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Dns extends IOTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(ConsoleContext $context)
    {
        $this->consoleHelper->writeTitle('Configure DNS resolution for Docker containers');

        // Configure virtual machine
        $authentication = new Password('docker', 'tcuser');
        $session = new Session(new Configuration('192.168.42.10'), $authentication);
        $exec = $session->getExec();
        try {
            $result = $exec->run('grep EXTRA_ARGS /var/lib/boot2docker/profile');
        } catch (\RuntimeException $e) {
            $result = null;
        }

        if (empty($result)) {
            $exec->run('echo EXTRA_ARGS=\"-H unix:///var/run/docker.sock --bip=172.17.42.1/16 --dns=172.17.42.1\" | sudo tee /var/lib/boot2docker/profile');
        }

        try {
            $result = $exec->run('grep dnsdock /var/lib/boot2docker/bootlocal.sh');
        } catch (\RuntimeException $e) {
            $result = null;
        }

        if (empty($result)) {
            $bootScript = 'sleep 5' . PHP_EOL .
                'docker start dnsdock || docker run -d -v /var/run/docker.sock:/var/run/docker.sock --name dnsdock -p 172.17.42.1:53:53/udp tonistiigi/dnsdock' . PHP_EOL;
            $exec->run('echo "' . $bootScript . '" | sudo tee -a /var/lib/boot2docker/bootlocal.sh');
        }

        // Configure host machine resolution
        $process = new Process('sudo mkdir -p /etc/resolver');
        $this->consoleHelper->runProcess($process, true);

        $process = new Process('echo "nameserver 172.17.42.1" | sudo tee /etc/resolver/docker');
        $this->consoleHelper->runProcess($process, true);

        // Restart dinghy
        $this->consoleHelper->writeTitle('Restarting Dinghy');
        $process = new Process('dinghy restart');
        $process->setTimeout(null);
        $this->consoleHelper->runProcess($process, true);
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
        return 'dns';
    }
}
