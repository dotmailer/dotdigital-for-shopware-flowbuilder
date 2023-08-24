<?php

namespace Dotdigital\Flow\Service\Client\V3;

use Dotdigital\Flow\Service\SystemConfigurationTrait;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Dotdigital\V3\Client as V3Client;

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

    /**
     * @return V3Client
     */
    public function getClient(): V3Client
    {
        return $this->client;
    }

    /**
     * Set up client.
     *
     * @return void
     */
    private function setupClient() {
        $this->client::setApiUser( $this->getApiUserName() );
        $this->client::setApiPassword( $this->getApiPassword() );
        $this->client::setApiEndpoint( $this->getApiEndpoint() );
    }
}