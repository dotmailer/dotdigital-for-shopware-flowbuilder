<?php

namespace Dotdigital\Flow\Service\Client;

use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientStruct;
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
     * @param RecipientCollection $recipients
     * @param int $campaignId
     * @param array<int, object> $personalisedValues
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendEmail(RecipientCollection $recipients, $campaignId, array $personalisedValues)
    {
        $body = [
            "toAddresses" => $recipients->reduce(function($list, RecipientStruct $email){
                $list[] = $email->getEmail();
                return $list;
            }, []),
            "campaignId" => $campaignId,
            "personalizationValues" => $personalisedValues
        ];

        $payload = json_encode($body);
        $this->post('/v2/email/triggered-campaign', ["body" => $payload]);
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
