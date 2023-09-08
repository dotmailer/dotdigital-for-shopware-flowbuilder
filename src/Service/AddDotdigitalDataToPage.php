<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service;

use Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent\SmsConsentPageStruct;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Setting\Settings;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoadedEvent;
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
        ];
    }

    public function addSmsSubscriptionStateToAccountOverviewPage(AccountOverviewPageLoadedEvent $event): void
    {
        if (!$event->getSalesChannelContext()->getCustomer()) {
            return;
        }

        $systemList = (int) $this->systemConfigService->get(Settings::LIST);
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
                ),
            ]);

            return;
        }

        $channels = $contact->getChannelProperties();
        $smsSubscriptionStatus = $channels?->getSms()?->getStatus();
        $contactLists = $contact->getLists() ?? [];
        $contactLists = array_column($contactLists, 'id');

        if (\in_array($systemList, $contactLists, true) && $smsSubscriptionStatus === 'subscribed') {
            $phoneNumber = $contact->getIdentifiers()->getMobileNumber();
            $event->getPage()->setExtensions([
                'dotdigital_sms_consent' => new SmsConsentPageStruct(
                    $phoneNumber ? '+' . $phoneNumber : '',
                    true,
                ),
            ]);
        }
    }
}
