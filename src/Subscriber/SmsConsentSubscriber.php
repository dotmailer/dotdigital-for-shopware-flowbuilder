<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Subscriber;

use Dotdigital\Flow\Core\Framework\DataTypes\SmsConsent\SmsConsentDataBag;
use Dotdigital\Flow\Service\Client\SmsConsentService;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Framework\Event\DataMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

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
            CheckoutOrderPlacedEvent::class => 'onCheckout',
        ];
    }

    /**
     * On account subscription update event listener for sms consent subscription update
     */
    public function onRegister(DataMappingEvent $event): void
    {
        $dataBag = new SmsConsentDataBag($event->getInput()->all());
        $salesChannelId = $event->getOutput()['salesChannelId'];
        if ($dataBag->has('ddg_sms_subscribed_name')) {
            $this->smsConsentService->subscribe(
                $dataBag,
                $salesChannelId
            );
        }
    }

    public function onCheckout(CheckoutOrderPlacedEvent $event): void
    {
        $request = Request::createFromGlobals();
        /** @var \Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity $customer */
        $customer = $event->getOrder()->getOrderCustomer();
        $dataBag = new SmsConsentDataBag($request->request->all());
        $dataBag->set('email', $customer->getEmail());
        $dataBag->set('firstName', $customer->getFirstName());
        $dataBag->set('lastName', $customer->getLastName());
        if ($dataBag->has('ddg_sms_subscribed_name')) {
            $this->smsConsentService->subscribe(
                $dataBag,
                $event->getSalesChannelId()
            );
        }
    }
}
