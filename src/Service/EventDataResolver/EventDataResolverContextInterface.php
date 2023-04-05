<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver;

use Dotdigital\Flow\Service\EventDataResolver\BuildStrategies\BuildStrategyInterface;
use Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies\ValidateStrategyInterface;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Struct\Collection;

interface EventDataResolverContextInterface
{
    /**
     * Resolve the event data
     */
    public function resolve(StorableFlow $flow): Collection;

    /**
     * Set the build strategy
     */
    public function setBuildStrategy(BuildStrategyInterface $buildStrategy): EventDataResolverContextInterface;

    /**
     * Set the validate strategy
     */
    public function setValidateStrategy(ValidateStrategyInterface $validateStrategy): EventDataResolverContextInterface;
}
