<?php

namespace DockerInstaller;

use DockerInstaller\Task\Boot2DockerCertificateWorkaround;
use DockerInstaller\Task\BrewCask;
use DockerInstaller\Task\Dinghy;
use DockerInstaller\Task\Dns;
use DockerInstaller\Task\DockerCompose;
use DockerInstaller\Task\DockerRouting;
use DockerInstaller\Task\Homebrew;
use DockerInstaller\Task\PhpSsh;
use DockerInstaller\Task\Vagrant;
use DockerInstaller\Task\VirtualBox;
use SRIO\ChainOfResponsibility\ChainBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Install Docker on OSX')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $builder = new ChainBuilder([
            new Homebrew(),
            new BrewCask(),
            new PhpSsh(),
            new Dinghy(),
            new Boot2DockerCertificateWorkaround(),
            new DockerRouting(),
            new Dns(),
            new Vagrant(),
            new VirtualBox(),
            new DockerCompose()
        ]);

        $runner = $builder->getRunner();
        $runner->run(new ConsoleContext($input, $output));
    }
}
