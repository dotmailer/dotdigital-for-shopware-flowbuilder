<?php

namespace Dotdigital\Flow\Service\Client;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClientFactory
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * DotdigitalClientFactory construct.
     *
     * @param LoggerInterface $logger
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        LoggerInterface $logger,
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function createClient(?string $salesChannelId = null): DotdigitalClient
    {
        return new DotdigitalClient(
            $this->systemConfigService,
            $this->logger,
            $salesChannelId
        );
    }
}
