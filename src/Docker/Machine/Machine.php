<?php

namespace Dock\Docker\Machine;

interface Machine
{
    /**
     * Returns true if the machine is running.
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Start the Docker machine.
     *
     * @throws MachineException
     */
    public function start();

    /**
     * Stops the Docker machine.
     *
     * @throws MachineException
     */
    public function stop();

    /**
     * Get docker machine IP.
     *
     * @throws MachineException
     *
     * @return string
     */
    public function getIp();

    /**
     * Returns true if the machine is created.
     *
     * @return bool
     */
    public function isCreated();

    /**
     * Creates the docker machine.
     *
     * @throws MachineException
     */
    public function create();
}
