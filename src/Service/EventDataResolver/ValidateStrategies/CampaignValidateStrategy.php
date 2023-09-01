<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\EventDataResolver\ValidateStrategies;

use Shopware\Core\Content\Flow\Dispatching\StorableFlow;

class CampaignValidateStrategy implements ValidateStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function validate(StorableFlow $flow): bool
    {
        $flowData = $flow->getConfig();

        if (!\array_key_exists('campaignId', $flowData)) {
            throw new \InvalidArgumentException('The campaignId value in the flow action configuration is invalid or missing.', 422);
        }

        return true;
    }
}
