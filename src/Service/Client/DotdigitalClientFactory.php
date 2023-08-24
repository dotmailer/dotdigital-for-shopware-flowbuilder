<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Dotdigital\Flow\Service\Client\V3\DotdigitalClient as V3DotdigitalClient;

class DotdigitalClientFactory
{
    /**
     * DotdigitalClientFactory construct.
     */
    public function __construct(
        private LoggerInterface $logger,
        private SystemConfigService $systemConfigService
    ) {}

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
