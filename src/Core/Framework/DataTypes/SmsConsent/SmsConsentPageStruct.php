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

    public function __construct(
        string $number,
        bool $isSubscribed
    ) {
        $this->number = $number;
        $this->isSubscribed = $isSubscribed;
    }
}
