<?php

namespace Dock\Docker\Machine;

use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Exec;
use Ssh\Session;

class SshClient
{
    const DEFAULT_USERNAME = 'docker';
    const DEFAULT_PASSWORD = 'tcuser';

    /**
     * @var \Ssh\Session
     */
    private $session;

    /**
     * @var Exec
     */
    private $exec;

    /**
     * @var Machine
     */
    private $machine;

    /**
     * @param Machine $machine
     */
    public function __construct(Machine $machine)
    {
        $this->machine = $machine;
    }

    /**
     * @param string $command
     *
     * @return string
     */
    public function run($command)
    {
        if (!$this->exec) {
            $this->exec = $this->getSession()->getExec();
        }

        return $this->exec->run($command);
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function runAndCheckOutputWasGenerated($command)
    {
        try {
            $result = $this->run($command);
        } catch (\RuntimeException $e) {
            $result = null;
        }

        return !empty($result);
    }

    /**
     * @return \Ssh\Sftp
     */
    public function getSftp()
    {
        return $this->getSession()->getSftp();
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        if (null == $this->session) {
            $this->session = new Session(
                new Configuration(
                    $this->machine->getIp()
                ),
                new Password(self::DEFAULT_USERNAME, self::DEFAULT_PASSWORD)
            );
        }

        return $this->session;
    }
}
