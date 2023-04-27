<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

interface ValidateStrategyInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function validate(StorableFlow $flowEvent): ?bool;
}
