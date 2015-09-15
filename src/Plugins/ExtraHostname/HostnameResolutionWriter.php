<?php

namespace Dock\Plugins\ExtraHostname;

interface HostnameResolutionWriter
{
    /**
     * Write a new hostname resolution.
     *
     * @param string $hostname
     * @param string $address
     *
     * @throws \RuntimeException
     */
    public function write($hostname, $address);
}
