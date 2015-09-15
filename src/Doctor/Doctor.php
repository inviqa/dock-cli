<?php

namespace Dock\Doctor;

use Symfony\Component\Console\Output\OutputInterface;

interface Doctor
{
    /**
     * @param OutputInterface $output
     * @param bool $dryRun
     */
    public function examine(OutputInterface $output, $dryRun);
}
