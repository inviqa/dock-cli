<?php

namespace Dock\Dinghy;

use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;

class SshClient
{
    private $session;
    /**
     * @var string
     */
    private $hostname;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     */
    public function __construct($hostname = '192.168.42.10', $username = 'docker', $password = 'tcuser')
    {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return \Ssh\Exec
     */
    public function getExec()
    {
        return $this->getSession()->getExec();
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        if (null === $this->session) {
            $this->session = new Session(
                new Configuration($this->hostname),
                new Password($this->username, $this->password)
            );
        }

        return $this->session;
    }
}
