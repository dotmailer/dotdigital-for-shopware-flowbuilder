<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationStruct;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

class PersonalisedValuesBuildStrategy implements BuildStrategyInterface
{
    private BusinessEventEncoder $businessEventEncoder;

    public function __construct(
        BusinessEventEncoder $businessEventEncoder
    ) {
        $this->businessEventEncoder = $businessEventEncoder;
    }

    /**
     * @inheritDoc
     */
    public function build(StorableFlow $flow): ContactPersonalisationCollection
    {
		$availableData = $this->businessEventEncoder->encodeData($flow->data(), $flow->stored());
        $personalisedValues = new ContactPersonalisationCollection();
        foreach ($availableData as $key => $value) {
            $personalisedValues->add(new ContactPersonalisationStruct($key, $value));
        }

        return $personalisedValues;
    }
}
