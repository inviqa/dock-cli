<?php

namespace Dock\Docker\Machine;

use Dock\IO\FileManipulator;

class SshFileManipulator implements FileManipulator
{
    /**
     * @var SshClient
     */
    private $sshClient;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @param SshClient $sshClient
     * @param string    $filePath
     */
    public function __construct(SshClient $sshClient, $filePath)
    {
        $this->sshClient = $sshClient;
        $this->filePath = $filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->sshClient->run('cat '.$this->filePath);
    }

    /**
     * {@inheritdoc}
     */
    public function write($contents)
    {
        return $this->sshClient->run('echo "'.str_replace('"', '\"', $contents).'" | sudo tee '.$this->filePath);
    }
}
