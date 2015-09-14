<?php

namespace Test\Doctor;

use Dock\Doctor\Doctor;
use Symfony\Component\Console\Output\OutputInterface;

class NullDoctor implements Doctor
{
    /**
     * {@inheritdoc}
     */
    public function examine(OutputInterface $output, $dryRun)
    {
    }
}
