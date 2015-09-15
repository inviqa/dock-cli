<?php

namespace Dock\Plugins\ExtraHostname;

use Dock\IO\ProcessRunner;

class HostsFileResolutionWriter implements HostnameResolutionWriter
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function write($hostname, $address)
    {
        $this->removeHostname($hostname);

        $hostsFilePath = $this->getFilePath();
        $contents = file_get_contents($hostsFilePath);
        $resolutionLine = $this->getHostsFileResolutionLine($hostname, $address);
        $contents .= PHP_EOL.$resolutionLine.PHP_EOL;

        $this->writeAsRoot($hostsFilePath, $contents);
    }

    /**
     * Remove hostname reference from `/etc/hosts`.
     *
     * @param string $hostname
     */
    private function removeHostname($hostname)
    {
        $filePath = $this->getFilePath();
        $quotedHostname = preg_quote($hostname);
        $fileContents = '';

        foreach (file($filePath) as $line) {
            $trimmedLine = trim($line);

            if (preg_match('#'.$quotedHostname.' \# dock-cli$#i', $trimmedLine)) {
                continue;
            }

            $fileContents .= $line;
        }

        $this->writeAsRoot($filePath, $fileContents);
    }

    /**
     * @return string
     */
    private function getFilePath()
    {
        return '/etc/hosts';
    }

    /**
     * @param string $targetPath
     * @param string $fileContents
     */
    private function writeAsRoot($targetPath, $fileContents)
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'hostFile');
        file_put_contents($temporaryFile, $fileContents);

        $this->processRunner->run(sprintf('sudo cp %s %s', $temporaryFile, $targetPath));
    }

    /**
     * @param string $hostname
     * @param string $address
     * @return string
     */
    private function getHostsFileResolutionLine($hostname, $address)
    {
        return $address.' '.$hostname.' # dock-cli';
    }
}
