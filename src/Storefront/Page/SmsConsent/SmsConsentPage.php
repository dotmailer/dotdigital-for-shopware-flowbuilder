<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Storefront\Page\SmsConsent;

use Dotdigital\V3\Models\Contact;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Storefront\Page\Page;

class SmsConsentPage extends Page
{
    /**
     * @var string
     */
    protected $systemAssignedList = '';

    /**
     * @var Contact|null
     */
    protected $contact;

    /**
     * @var CustomerEntity
     */
    protected $customer;

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact = null): void
    {
        $this->contact = $contact;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getSystemAssignedList(): ?int
    {
        return (int) $this->systemAssignedList;
    }

    /**
     * @param mixed $systemAssignedList
     */
    public function setSystemAssignedList($systemAssignedList): void
    {
        $this->systemAssignedList = $systemAssignedList;
    }

    public function getContactPhoneNumber(): string
    {
        if ($this->contact === null) {
            return '';
        }

        $phoneNumber = $this->contact->getIdentifiers()->getMobileNumber();

        return $phoneNumber ? '+' . $phoneNumber : '';
    }

    public function getContactIsSubscribed(): bool
    {
        if ($this->contact === null) {
            return false;
        }

        $channels = $this->contact->getChannelProperties();
        $smsSubscriptionStatus = $channels?->getSms()->getStatus();
        $contactLists = $this->contact->getLists() ?? [];
        $contactLists = array_column($contactLists, 'id');

        if (\in_array($this->getSystemAssignedList(), $contactLists, true) && $smsSubscriptionStatus === 'subscribed') {
            return true;
        }

        return false;
    }
}
