<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\CampaignCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\CampaignStruct;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class CampaignBuildStrategy implements BuildStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function build(StorableFlow $flow): CampaignCollection
    {
        $flowData = $flow->getConfig();
        $campaignCollection = new CampaignCollection();
        $campaignCollection->add(new CampaignStruct((int) $flowData['campaignId']));

        return $campaignCollection;
    }
}
