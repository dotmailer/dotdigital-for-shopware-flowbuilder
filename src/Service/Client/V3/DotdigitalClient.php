<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client\V3;

use Dotdigital\Flow\Service\SystemConfigurationTrait;
use Dotdigital\V3\Client as V3Client;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClient
{
    use SystemConfigurationTrait;

    private V3Client $client;

    /**
     * DotdigitalClient construct.
     */
    public function __construct(
        private SystemConfigService $systemConfigService,
        private LoggerInterface $logger,
        private ?string $salesChannelId = null
    ) {
        $this->client = new V3Client();
        $this->setupClient();
    }

    public function getClient(): V3Client
    {
        return $this->client;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Set up client.
     */
    private function setupClient(): void
    {
        $this->client::setApiUser($this->getApiUserName());
        $this->client::setApiPassword($this->getApiPassword());
        $this->client::setApiEndpoint($this->getApiEndpoint());
    }
}
