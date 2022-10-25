<?php

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataFieldCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactDataFieldStruct;

trait InteractWithContactDataFieldsTrait
{
    use UtilitiesTrait;

    /**
     * @param int $count
     * @return array
     */
    protected function generateContactDataFieldArray(int $count = 1): array
    {
        $contactDataFields = [];
        for ($i = 0; $i < $count; $i++) {
            $contactDataFields[] = [
                'key' => $this->generateRandomString(),
                'value' => $this->generateRandomString()
            ];
        }
        return $contactDataFields;
    }

    /**
     * @return ContactDataFieldStruct
     */
    protected function generateContactDataField(): ContactDataFieldStruct
    {
        return new ContactDataFieldStruct(
            $this->generateRandomString(),
            $this->generateRandomString()
        );
    }

    protected function generateContactDataFieldCollection(int $count = 1): ContactDataFieldCollection
    {
        $contactDataFields = new ContactDataFieldCollection();
        for ($i = 0; $i < $count; $i++) {
            $contactDataFields->add($this->generateContactDataField());
        }
        return $contactDataFields;
    }

}
