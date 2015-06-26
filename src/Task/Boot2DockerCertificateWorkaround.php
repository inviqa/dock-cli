<?php

namespace DockerInstaller\Task;

use DockerInstaller\ConsoleContext;
use SRIO\ChainOfResponsibility\DependentChainProcessInterface;
use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Boot2DockerCertificateWorkaround extends IOTask implements DependentChainProcessInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(ConsoleContext $context)
    {
        $this->consoleHelper->writeTitle('Setup the boot2docker certificate workaround');

        // Configure virtual machine
        $authentication = new Password('docker', 'tcuser');
        $session = new Session(new Configuration('192.168.42.10'), $authentication);
        $exec = $session->getExec();
        try {
            $result = $exec->run('grep wait4eth1 /var/lib/boot2docker/profile');
        } catch (\RuntimeException $e) {
            $result = null;
        }

        if (empty($result)) {
            $wait4eth1 = <<<EOF
wait4eth1() {
        CNT=0
        until ip a show eth1 | grep -q UP
        do
                [ $((CNT++)) -gt 60 ] && break || sleep 1
        done
        sleep 1
}
wait4eth1
EOF;

            $exec->run('echo "'.$wait4eth1.'" | sudo tee /var/lib/boot2docker/profile');

            // Restart dinghy
            $this->consoleHelper->writeTitle('Restarting Dinghy');
            $process = new Process('dinghy restart');
            $process->setTimeout(null);
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
        return 'boot2dockerworkarround';
    }
}
