<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Struct\Collection;

interface BuildStrategyInterface
{
    public function build(FlowEvent $flowEvent): Collection;
}
