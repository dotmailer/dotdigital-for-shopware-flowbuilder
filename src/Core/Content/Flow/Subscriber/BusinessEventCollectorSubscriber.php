<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\Flow\Subscriber;

use Dotdigital\Flow\Core\Framework\Event\DotdigitalEmailSenderAware;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
     */
    public function addDotdigitalEmailSenderAware(BusinessEventCollectorEvent $event): void
    {
        foreach ($event->getCollection()->getElements() as $definition) {
            $definition->addAware(DotdigitalEmailSenderAware::class);
        }
    }
}
