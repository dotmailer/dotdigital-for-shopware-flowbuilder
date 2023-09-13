<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent;

use Shopware\Core\Framework\Validation\DataBag\DataBag;

class SmsConsentDataBag extends DataBag
{
    public function getEmail(): string
    {
        return $this->get('email', '');
    }

    public function getPhone(): string
    {
        return $this->get('phone', '');
    }

    public function getFirstName(): string
    {
        return $this->get('firstName', '');
    }

    public function getLastName(): string
    {
        return $this->get('lastName', '');
    }

    public function getConsentCheckbox(): bool
    {
        return (bool) $this->get('consentCheckbox', false);
    }
}
