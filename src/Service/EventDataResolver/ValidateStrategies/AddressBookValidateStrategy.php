<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class AddressBookValidateStrategy implements ValidateStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function validate(StorableFlow $flow): bool
    {
        $flowData = $flow->getConfig();
        if (!\array_key_exists('addressBook', $flowData)) {
            throw new \InvalidArgumentException('The addressBook value in the flow action configuration is invalid or missing.', 422);
        }

        return true;
    }
}
