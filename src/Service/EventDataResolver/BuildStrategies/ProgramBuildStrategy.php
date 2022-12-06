<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\ProgramCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramStruct;
use Shopware\Core\Framework\Event\FlowEvent;

class ProgramBuildStrategy implements BuildStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function build(FlowEvent $flowEvent): ProgramCollection
    {
        $eventData = $flowEvent->getConfig();
        $programCollection = new ProgramCollection();
        $programCollection->add(new ProgramStruct($eventData['programId']));

        return $programCollection;
    }
}
