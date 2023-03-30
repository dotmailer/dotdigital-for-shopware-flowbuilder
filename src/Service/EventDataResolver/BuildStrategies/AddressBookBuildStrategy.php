<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class AddressBookBuildStrategy implements BuildStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function build(StorableFlow $flow): AddressBookCollection
    {
        $flowData = $flow->getConfig();
        $addressBookCollection = new AddressBookCollection();
        $addressBookCollection->add(new AddressBookStruct($flowData['addressBook']));

        return $addressBookCollection;
    }
}
