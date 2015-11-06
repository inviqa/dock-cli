<?php

namespace Dock\Installer\System\Catcher;

use Dock\IO\UserInteraction;
use SRIO\ChainOfResponsibility\ChainProcessInterface;
use SRIO\ChainOfResponsibility\DecoratorFactoryInterface;

class ErrorCatcherDecoratorFactory implements DecoratorFactoryInterface
{
    /**
     * @var DecoratorFactoryInterface
     */
    private $decoratedFactory;
    /**
     * @var UserInteraction
     */
    private $userInteraction;

    /**
     * @param DecoratorFactoryInterface $decoratedFactory
     * @param UserInteraction           $userInteraction
     */
    public function __construct(DecoratorFactoryInterface $decoratedFactory, UserInteraction $userInteraction)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->userInteraction = $userInteraction;
    }

    /**
     * {@inheritdoc}
     */
    public function decorate(ChainProcessInterface $process, ChainProcessInterface $next = null)
    {
        return new XcodeLicenseErrorCatcherDecorator(
            $this->userInteraction,
            $this->decoratedFactory->decorate($process, $next)
        );
    }
}
