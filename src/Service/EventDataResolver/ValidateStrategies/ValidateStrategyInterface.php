<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Framework\Event\FlowEvent;

interface ValidateStrategyInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function validate(FlowEvent $flowEvent): ?bool;
}
