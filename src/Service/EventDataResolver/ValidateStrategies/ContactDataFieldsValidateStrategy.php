<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Framework\Event\FlowEvent;

class ContactDataFieldsValidateStrategy implements ValidateStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function validate(FlowEvent $flowEvent): bool
    {
        $eventData = $flowEvent->getConfig();

        if (!\array_key_exists('dataFields', $eventData)) {
            throw new \InvalidArgumentException('Data fields are not set');
        }

        if (!\is_array($eventData['dataFields'])) {
            throw new \InvalidArgumentException('Data fields are not an array');
        }

        foreach ($eventData['dataFields'] as $dataField) {
            if (!\array_key_exists('key', $dataField)) {
                throw new \InvalidArgumentException('Data field key is not set');
            }

            if (!\array_key_exists('value', $dataField)) {
                throw new \InvalidArgumentException('Data field value is not set');
            }
        }

        return true;
    }
}
