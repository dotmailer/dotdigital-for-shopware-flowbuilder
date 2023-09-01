<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service;

use Dotdigital\Flow\Setting\Settings;

trait SystemConfigurationTrait
{
    protected function getApiUserName(): string
    {
        return $this->systemConfigService->getString(
            Settings::AUTHENTICATION_USERNAME_CONFIG_KEY,
            $this->salesChannelId
        );
    }

    protected function getApiPassword(): string
    {
        return $this->systemConfigService->getString(
            Settings::AUTHENTICATION_PASSWORD_CONFIG_KEY,
            $this->salesChannelId
        );
    }

    protected function getApiEndpoint(): string
    {
        $region = $this->systemConfigService->getString(
            Settings::HOST_REGION_CONFIG_KEY,
            $this->salesChannelId
        );
        $host = Settings::HOST;

        return "https://{$region}-{$host}";
    }

    protected function getConsentText(): string
    {
        return $this->systemConfigService->getString(
            Settings::CONSENT_TEXT,
            $this->salesChannelId
        );
    }

    protected function getList(): int
    {
        return $this->systemConfigService->getInt(
            Settings::LIST,
            $this->salesChannelId
        );
    }
}
