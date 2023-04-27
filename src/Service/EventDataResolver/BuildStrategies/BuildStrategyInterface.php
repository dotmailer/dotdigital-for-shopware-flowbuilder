<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Struct\Collection;

interface BuildStrategyInterface
{
    public function build(StorableFlow $flowEvent): Collection;
}
