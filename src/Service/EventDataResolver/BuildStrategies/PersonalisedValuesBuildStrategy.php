<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationStruct;
use Shopware\Core\Framework\Event\FlowEvent;
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
    public function build(FlowEvent $flowEvent): ContactPersonalisationCollection
    {
        $businessEvent = $this->businessEventEncoder->encode($flowEvent->getEvent());
        $personalisedValues = new ContactPersonalisationCollection();
        foreach ($businessEvent as $key => $value) {
            $personalisedValues->add(new ContactPersonalisationStruct($key, $value));
        }

        return $personalisedValues;
    }
}
