<?php

namespace Dock\Docker\Machine;

interface Machine
{
    public function isRunning();
    public function start();
    public function stop();
    public function getIp();

    public function isCreated();
    public function create();
}
