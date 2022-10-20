<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClientFactory
{
    private LoggerInterface $logger;

    private SystemConfigService $systemConfigService;

    /**
     * DotdigitalClientFactory construct.
     */
    public function __construct(
        LoggerInterface $logger,
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    public function createClient(?string $salesChannelId = null): DotdigitalClient
    {
        return new DotdigitalClient(
            $this->systemConfigService,
            $this->logger,
            $salesChannelId
        );
    }
}
