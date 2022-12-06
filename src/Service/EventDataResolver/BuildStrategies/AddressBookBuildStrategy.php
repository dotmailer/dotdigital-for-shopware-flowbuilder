<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;
use Shopware\Core\Framework\Event\FlowEvent;

class AddressBookBuildStrategy implements BuildStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function build(FlowEvent $flowEvent): AddressBookCollection
    {
        $eventData = $flowEvent->getConfig();
        $addressBookCollection = new AddressBookCollection();
        $addressBookCollection->add(new AddressBookStruct($eventData['addressBook']));

        return $addressBookCollection;
    }
}
