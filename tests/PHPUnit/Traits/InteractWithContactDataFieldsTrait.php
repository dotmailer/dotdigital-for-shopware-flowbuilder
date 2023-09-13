<?php
declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataStruct;

trait InteractWithContactDataFieldsTrait
{
    use UtilitiesTrait;

    protected function generateContactDataFieldArray(int $count = 1): array
    {
        $contactDataFields = [];
        for ($i = 0; $i < $count; ++$i) {
            $contactDataFields[] = [
                'key' => $this->generateRandomString(),
                'value' => $this->generateRandomString(),
            ];
        }

        return $contactDataFields;
    }

    protected function generateContactDataFieldStruct(): ContactDataStruct
    {
        return (new ContactDataStruct())
            ->setKey($this->generateRandomString())
            ->setValue($this->generateRandomString());
    }

    protected function generateContactDataFieldCollection($count = 1): ContactDataCollection
    {
        $contactDataFieldCollection = new ContactDataCollection();
        for ($i = 0; $i < $count; ++$i) {
            $contactDataFieldCollection->add($this->generateContactDataFieldStruct());
        }

        return $contactDataFieldCollection;
    }
}
