<?php
declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\CampaignCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\CampaignStruct;

trait InteractWithCampaignsTrait
{
    use InteractWithContactDataFieldsTrait;
    use UtilitiesTrait;

    /**
     * @throws \Exception
     */
    protected function generateCampaign(): CampaignStruct
    {
        return CampaignStruct::createFromResponse([
            'id' => $this->generateInteger(),
            'name' => 'chaz campaign',
            'subject' => 'Greetings',
            'fromName' => 'Chaz',
            'replyAction' => 'undefined',
            'replyToAction' => 'undefined',
            'isSplitTest' => false,
            'status' => CampaignStruct::STATUSES[0],
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function generateCampaignCollection(int $count = 1): CampaignCollection
    {
        $campaigns = new CampaignCollection();
        for ($i = 0; $i < $count; ++$i) {
            $campaigns->add($this->generateCampaign());
        }

        return $campaigns;
    }
}
