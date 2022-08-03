<?php

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientStruct;

trait InteractWithRecipientsTrait
{
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

    /**
     * @param float $length
     * @return false|string
     */
    private function generateRandomString(float $length = 10.0) {
        /* @phpstan-ignore-next-line */
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @return string
     */
    private function generateValidEmail():string
    {
        return $this->generateRandomString(5).'@'.$this->generateRandomString(5).'.test';
    }
}
