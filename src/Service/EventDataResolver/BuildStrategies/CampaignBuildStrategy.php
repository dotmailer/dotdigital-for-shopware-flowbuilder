<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\BuildStrategies;

use Dotdigital\Flow\Core\Framework\DataTypes\CampaignCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\CampaignStruct;
use Shopware\Core\Framework\Event\FlowEvent;

class CampaignBuildStrategy implements BuildStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function build(FlowEvent $flowEvent): CampaignCollection
    {
        $eventData = $flowEvent->getConfig();
        $campaignCollection = new CampaignCollection();
        $campaignCollection->add(new CampaignStruct((int) $eventData['campaignId']));

        return $campaignCollection;
    }
}
