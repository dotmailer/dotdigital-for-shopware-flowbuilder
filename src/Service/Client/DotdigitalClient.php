<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Client;

use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientStruct;
use Dotdigital\Flow\Setting\Settings;
use GuzzleHttp\Client as Guzzle;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class DotdigitalClient extends AbstractClient
{
    private SystemConfigService $systemConfigService;

    private ?string $salesChannelId;

    /**
     * DotdigitalClient construct.
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
                'Content-Type' => 'application/json',
            ],
        ]);

        parent::__construct($client, $logger);
    }

    /**
     * @param int                              $campaignId
     * @param array<int, array<string, mixed>> $personalisedValues
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendEmail(RecipientCollection $recipients, $campaignId, array $personalisedValues): void
    {
        $body = [
            'toAddresses' => $recipients->reduce(function ($list, RecipientStruct $email) {
                $list[] = $email->getEmail();

                return $list;
            }, []),
            'campaignId' => $campaignId,
            'personalizationValues' => $personalisedValues,
        ];

        $payload = json_encode($body);
        $this->post('/v2/email/triggered-campaign', ['body' => $payload]);
    }

    /**
     * Get base url.
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
