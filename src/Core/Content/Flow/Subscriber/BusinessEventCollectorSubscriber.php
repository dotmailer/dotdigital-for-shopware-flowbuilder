<?php

namespace Dotdigital\Flow\Core\Content\Flow\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Dotdigital\Flow\Core\Framework\Event\DotdigitalEmailSenderAware;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;

class BusinessEventCollectorSubscriber implements EventSubscriberInterface
{
    /**
     * Get subscribed events.
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            BusinessEventCollectorEvent::NAME => 'addDotdigitalEmailSenderAware',
        ];
    }

    /**
     * Add Dotdigital email sender aware.
     *
     * @param BusinessEventCollectorEvent $event
     * @return void
     */
    public function addDotdigitalEmailSenderAware(BusinessEventCollectorEvent $event): void
    {
        foreach ($event->getCollection()->getElements() as $definition) {
            $definition->addAware(DotdigitalEmailSenderAware::class);
        }
    }
}
