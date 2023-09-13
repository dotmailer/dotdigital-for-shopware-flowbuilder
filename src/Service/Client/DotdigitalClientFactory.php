<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use Dotdigital\Flow\Service\Client\V3\DotdigitalClient as V3DotdigitalClient;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClientFactory
{
    /**
     * DotdigitalClientFactory construct.
     */
    public function __construct(
        private LoggerInterface $logger,
        private SystemConfigService $systemConfigService
    ) {
    }

    public function createClient(?string $salesChannelId = null): DotdigitalClient
    {
        return new DotdigitalClient(
            $this->systemConfigService,
            $this->logger,
            $salesChannelId
        );
    }

    public function createV3Client(?string $salesChannelId = null): V3DotdigitalClient
    {
        return new V3DotdigitalClient(
            $this->systemConfigService,
            $this->logger,
            $salesChannelId
        );
    }
}
