<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Framework\Event\FlowEvent;

class ContactValidateStrategy implements ValidateStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function validate(FlowEvent $flowEvent): bool
    {
        $eventData = $flowEvent->getConfig();

        if (!\array_key_exists('recipient', $eventData)) {
            throw new \InvalidArgumentException('The contactEmail value in the flow action configuration is invalid or missing.', 422);
        }

        if (!\array_key_exists('data', $eventData['recipient'])) {
            throw new \InvalidArgumentException('The contactEmail value in the flow action configuration is invalid or missing.', 422);
        }

        if (!\array_key_exists('type', $eventData['recipient'])) {
            throw new \InvalidArgumentException('The contactEmail value in the flow action configuration is invalid or missing.', 422);
        }

        return true;
    }
}
