<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class ContactDataFieldsValidateStrategy implements ValidateStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function validate(StorableFlow $flow): bool
    {
        $flowData = $flow->getConfig();

        if (!\array_key_exists('dataFields', $flowData)) {
            throw new \InvalidArgumentException('Data fields are not set');
        }

        if (!\is_array($flowData['dataFields'])) {
            throw new \InvalidArgumentException('Data fields are not an array');
        }

        foreach ($flowData['dataFields'] as $dataField) {
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
