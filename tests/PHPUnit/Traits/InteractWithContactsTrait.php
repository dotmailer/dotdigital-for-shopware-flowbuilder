<?php
declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactStruct;

trait InteractWithContactsTrait
{
    use InteractWithContactDataFieldsTrait;
    use UtilitiesTrait;

    /**
     * @throws \Exception
     */
    protected function generateContact(): ContactStruct
    {
        return ContactStruct::createFromResponse([
            'id' => $this->generateInteger(),
            'email' => $this->generateValidEmail(),
            'emailType' => 'Html',
            'optInType' => 'Active',
            'dataFields' => $this->generateContactDataFieldArray(10),
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function generateContactCollection(int $count = 1): ContactCollection
    {
        $contacts = new ContactCollection();
        for ($i = 0; $i < $count; ++$i) {
            $contacts->add($this->generateContact());
        }

        return $contacts;
    }
}
