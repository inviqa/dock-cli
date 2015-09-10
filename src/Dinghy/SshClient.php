<?php

namespace Dock\Dinghy;

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
     * @var DinghyCli
     */
    private $dinghy;

    /**
     * @param DinghyCli $dinghy
     */
    public function __construct(DinghyCli $dinghy)
    {
        $this->dinghy = $dinghy;
    }

    /**
     * @param string $command
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
     * @return Session
     */
    private function getSession()
    {
        if (null == $this->session) {
            $this->session = new Session(
                new Configuration(
                    $this->dinghy->getIp()
                ),
                new Password(SshClient::DEFAULT_USERNAME, SshClient::DEFAULT_PASSWORD)
            );
        }

        return $this->session;
    }
}
