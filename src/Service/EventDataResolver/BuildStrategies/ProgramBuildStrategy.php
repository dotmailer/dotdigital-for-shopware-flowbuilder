<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\ProgramCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ProgramStruct;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class ProgramBuildStrategy implements BuildStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function build(StorableFlow $flow): ProgramCollection
    {
        $flowData = $flow->getConfig();
        $programCollection = new ProgramCollection();
        $programCollection->add(new ProgramStruct($flowData['programId']));

        return $programCollection;
    }
}
