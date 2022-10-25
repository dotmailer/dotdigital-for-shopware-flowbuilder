<?php

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientStruct;

trait InteractWithRecipientsTrait
{

    use UtilitiesTrait;

    /**
     * @param int $count
     * @return RecipientCollection
     */
    protected function generateValidRecipientCollection(int $count = 10): RecipientCollection
    {
        $recipientCollection = new RecipientCollection();
        for ($i = 1; $i <= $count; $i++) {
            $recipientCollection->add(
                new RecipientStruct($this->generateValidEmail())
            );
        }
        return $recipientCollection;
    }

}
