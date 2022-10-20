<?php declare(strict_types=1);

namespace Dotdigital\Flow\Setting;

use Monolog\Logger;

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
     * System Logger
     */
    public const DEFAULT_LOGGING_LEVEL = Logger::WARNING;
}
