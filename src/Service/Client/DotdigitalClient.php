<?php

namespace Dotdigital\Flow\Service\Client;

use GuzzleHttp\Client as Guzzle;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Dotdigital\Flow\Setting\Settings;

class DotdigitalClient extends AbstractClient
{
    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var string|null
     */
    private ?string $salesChannelId;

    /**
     * DotdigitalClient construct.
     *
     * @param SystemConfigService $systemConfigService
     * @param LoggerInterface $logger
     * @param string|null $salesChannelId
     */
    public function __construct(
        SystemConfigService $systemConfigService,
        LoggerInterface $logger,
        ?string $salesChannelId = null
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->salesChannelId = $salesChannelId;

        $client = new Guzzle([
            'base_uri' => $this->getBaseUrl(),
            'headers' => [
                'Accept' => 'text/plain',
                'Authorization' => 'Basic ' . $this->generateScopedAuthorizationToken(),
                'Content-Type' => 'application/json'
            ],
        ]);

        parent::__construct($client, $logger);
    }

    /**
     * Send triggered campaign to Dotdigital.
     *
     * @param string $toAddress
     * @param int $campaignId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendEmail($toAddress, $campaignId)
    {
        $this->post('/v2/email/triggered-campaign', [
            'body' => '{"toAddresses":[' . "'{$toAddress}'" . '], "campaignId":' . $campaignId . '}'
        ]);
    }

    /**
     * Get base url.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        $region = $this->systemConfigService->getString(
            Settings::HOST_REGION_CONFIG_KEY,
            $this->salesChannelId
        );
        $host = Settings::HOST;
        return "https://{$region}-{$host}";
    }

    /**
     * Generate Authorization token.
     *
     * @return string
     */
    public function generateScopedAuthorizationToken(): string
    {
        $usernameConfigurationValue = $this->systemConfigService->getString(
            Settings::AUTHENTICATION_USERNAME_CONFIG_KEY,
            $this->salesChannelId
        );
        $passwordConfigurationValue = $this->systemConfigService->getString(
            Settings::AUTHENTICATION_PASSWORD_CONFIG_KEY,
            $this->salesChannelId
        );
        return base64_encode(
            "{$usernameConfigurationValue}:{$passwordConfigurationValue}"
        );
    }
}
