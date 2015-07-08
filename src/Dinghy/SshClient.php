<?php

namespace Dock\Dinghy;

use Ssh\Exec;
use Ssh\Session;

class SshClient
{
    const DEFAULT_HOSTNAME = '192.168.42.10';
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
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $command
     * @return string
     */
    public function run($command)
    {
        if (!$this->exec) {
            $this->exec = $this->session->getExec();
        }

        return $this->exec->run($command);
    }
}
