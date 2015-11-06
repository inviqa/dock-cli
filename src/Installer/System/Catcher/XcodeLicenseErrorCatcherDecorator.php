<?php

namespace Dock\Installer\System\Catcher;

use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainContext;
use SRIO\ChainOfResponsibility\ChainProcessInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class XcodeLicenseErrorCatcherDecorator implements ChainProcessInterface
{
    /**
     * @var ChainProcessInterface
     */
    private $process;

    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param UserInteraction       $userInteraction
     * @param ChainProcessInterface $process
     */
    public function __construct(UserInteraction $userInteraction, ChainProcessInterface $process)
    {
        $this->process = $process;
        $this->userInteraction = $userInteraction;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ChainContext $context)
    {
        try {
            $this->process->execute($context);
        } catch (ProcessFailedException $e) {
            $processOutput = $e->getProcess()->getOutput();
            if (strpos($processOutput, 'Agreeing to the Xcode/iOS license requires admin privileges, please re-run') !== false) {
                $this->userInteraction->writeTitle('You need to agree Xcode license.');
                $this->userInteraction->write('Run `xcode-select --install` to fix the problem and run this command again.');

                throw $e;
            }
        }
    }
}
