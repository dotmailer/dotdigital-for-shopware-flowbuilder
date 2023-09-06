<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Subscriber;

use Dotdigital\Flow\Service\Client\SmsConsentService;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Framework\Event\DataMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SmsConsentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SmsConsentService $smsConsentService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CustomerEvents::MAPPING_REGISTER_CUSTOMER => 'onRegister',
        ];
    }

    /**
     * On account subscription update event listener for sms consent subscription update
     */
    public function onRegister(DataMappingEvent $event): void
    {
        $salesChannelId = $event->getOutput()['salesChannelId'];
        $this->smsConsentService->subscribe(
            $event->getInput(),
            $salesChannelId
        );
    }
}
