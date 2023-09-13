<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service;

use Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent\SmsConsentPageStruct;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Setting\Settings;
use Dotdigital\V3\Models\Contact;
use Http\Client\Exception;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddDotdigitalDataToPage implements EventSubscriberInterface
{
    public function __construct(
        private DotdigitalClientFactory $dotdigitalClientFactory,
        private SystemConfigService $systemConfigService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AccountOverviewPageLoadedEvent::class => 'addSmsSubscriptionStateToAccountOverviewPage',
            CheckoutConfirmPageLoadedEvent::class => 'addSmsSubscriptionStateToCheckoutPage',
        ];
    }

    /**
     * Add the sms subscription state to the checkout page.
     *
     * @throws Exception
     */
    public function addSmsSubscriptionStateToCheckoutPage(CheckoutConfirmPageLoadedEvent $event): void
    {
        if (!$event->getSalesChannelContext()->getCustomer()) {
            return;
        }
        $showSmsConsent = (bool) $this->systemConfigService->get(Settings::SHOW_CHECKOUT_SMS_CONSENT);
        $isGuest = $event->getSalesChannelContext()->getCustomer()->getGuest();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();
        $customer = $event->getSalesChannelContext()->getCustomer();

        try {
            $contact = $this->dotdigitalClientFactory
                ->createV3Client($salesChannelId)
                ->getClient()
                ->contacts
                ->getByIdentifier($customer->getEmail());
        } catch (\Dotdigital\Exception\ResponseValidationException $e) {
            $event->getPage()->setExtensions([
                'dotdigital_sms_consent' => new SmsConsentPageStruct(
                    '',
                    false,
                    $showSmsConsent,
                ),
            ]);

            return;
        }

        if ($this->isSubscribed($contact)) {
            $phoneNumber = $contact->getIdentifiers()->getMobileNumber();
            $event->getPage()->setExtensions([
                'dotdigital_sms_consent' => new SmsConsentPageStruct(
                    $phoneNumber ? '+' . $phoneNumber : '',
                    true,
                    $showSmsConsent,
                    $isGuest
                ),
            ]);
        }
    }

    /**
     * Add the sms subscription state to the account overview page.
     *
     * @throws Exception
     */
    public function addSmsSubscriptionStateToAccountOverviewPage(AccountOverviewPageLoadedEvent $event): void
    {
        if (!$event->getSalesChannelContext()->getCustomer()) {
            return;
        }

        $showSmsConsent = (bool) $this->systemConfigService->get(Settings::SHOW_ACCOUNT_SMS_CONSENT);
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();

        try {
            $contact = $this->dotdigitalClientFactory
                ->createV3Client($salesChannelId)
                ->getClient()
                ->contacts
                ->getByIdentifier($event->getSalesChannelContext()->getCustomer()->getEmail());
        } catch (\Dotdigital\Exception\ResponseValidationException $e) {
            $event->getPage()->setExtensions([
                'dotdigital_sms_consent' => new SmsConsentPageStruct(
                    '',
                    false,
                    $showSmsConsent,
                    false
                ),
            ]);

            return;
        }

        if ($this->isSubscribed($contact)) {
            $phoneNumber = $contact->getIdentifiers()->getMobileNumber();
            $event->getPage()->setExtensions([
                'dotdigital_sms_consent' => new SmsConsentPageStruct(
                    $phoneNumber ? '+' . $phoneNumber : '',
                    true,
                    $showSmsConsent,
                    false
                ),
            ]);
        }
    }

    /**
     * Is the contact subscribed to the system list?
     */
    private function isSubscribed(Contact $contact): bool
    {
        $systemList = (int) $this->systemConfigService->get(Settings::LIST);
        $channels = $contact->getChannelProperties();
        $smsSubscriptionStatus = $channels?->getSms()?->getStatus();
        $contactLists = array_column($contact->getLists() ?? [], 'id');

        return \in_array($systemList, $contactLists, true) && $smsSubscriptionStatus === 'subscribed';
    }
}
