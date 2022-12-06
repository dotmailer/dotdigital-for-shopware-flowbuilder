<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver;

use Psr\Log\LoggerInterface;

class EventDataResolverExceptionHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle the exception
     */
    public function handle(\Throwable $exception): void
    {
        $this->logger->error($exception->getMessage(), $exception->getTrace());
    }
}
