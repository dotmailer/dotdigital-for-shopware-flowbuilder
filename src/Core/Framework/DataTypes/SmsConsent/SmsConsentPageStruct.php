<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent;

use Dotdigital\Flow\Core\Framework\DataTypes\AbstractStruct;

class SmsConsentPageStruct extends AbstractStruct
{
    /**
     * @var string
     */
    public $number;

    /**
     * @var bool
     */
    public $isSubscribed;

    /**
     * @var bool
     */
    public $consentEnabled;

    /**
     * @var bool
     */
    public $isAuthedGuest;

    public function __construct(
        string $number,
        bool $isSubscribed,
        bool $consentEnabled = false,
        bool $isAuthedGuest = false
    ) {
        $this->number = $number;
        $this->isSubscribed = $isSubscribed;
        $this->consentEnabled = $consentEnabled;
        $this->isAuthedGuest = $isAuthedGuest;
    }

    public function showSmsConsent(): bool
    {
        if (!$this->consentEnabled) {
            return false;
        }

        if ($this->isAuthedGuest && $this->isSubscribed) {
            return false;
        }

        return true;
    }
}
