<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Framework\Event\FlowEvent;

class ProgramValidateStrategy implements ValidateStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function validate(FlowEvent $flowEvent): bool
    {
        $eventData = $flowEvent->getConfig();
        if (!\array_key_exists('programId', $eventData)) {
            throw new \InvalidArgumentException('The program value in the flow action configuration is invalid or missing.', 422);
        }

        return true;
    }
}
