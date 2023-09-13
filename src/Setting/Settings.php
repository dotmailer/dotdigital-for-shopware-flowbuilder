<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Setting;

class Settings
{
    /**
     * Plugin System base paths
     */
    public const DOTDIGITAL_CONFIG_DOMAIN = 'DotdigitalFlow.config.';

    /**
     * API Configuration
     */
    public const HOST = 'api.dotdigital.com';
    public const HOST_REGION_CONFIG_KEY = self::DOTDIGITAL_CONFIG_DOMAIN . 'region';

    /**
     * API authentication configuration keys
     */
    public const AUTHENTICATION_USERNAME_CONFIG_KEY = self::DOTDIGITAL_CONFIG_DOMAIN . 'username';
    public const AUTHENTICATION_PASSWORD_CONFIG_KEY = self::DOTDIGITAL_CONFIG_DOMAIN . 'password';

    /**
     * Date time formatting
     */
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s.v';

    /**
     * Consent settings
     */
    public const SHOW_ACCOUNT_SMS_CONSENT = self::DOTDIGITAL_CONFIG_DOMAIN . 'consentCaptureAccount';
    public const SHOW_CHECKOUT_SMS_CONSENT = self::DOTDIGITAL_CONFIG_DOMAIN . 'consentCaptureCheckout';
    public const CONSENT_TEXT = self::DOTDIGITAL_CONFIG_DOMAIN . 'consentCheckboxText';
    public const LIST = self::DOTDIGITAL_CONFIG_DOMAIN . 'dotdigitalList';
}
