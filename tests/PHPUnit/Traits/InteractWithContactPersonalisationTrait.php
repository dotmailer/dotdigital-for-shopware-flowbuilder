<?php declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\ContactPersonalisationStruct;

trait InteractWithContactPersonalisationTrait
{
    use UtilitiesTrait;

    protected function generateContactPersonalisationStruct(): ContactPersonalisationStruct
    {
        return (new ContactPersonalisationStruct())
            ->setName($this->generateRandomString())
            ->setValue($this->generateRandomString());
    }

    protected function generateContactPersonalisationCollection($count = 1): ContactPersonalisationCollection
    {
        $contactPersonalisationCollection = new ContactPersonalisationCollection();
        for ($i = 0; $i < $count; ++$i) {
            $contactPersonalisationCollection->add($this->generateContactPersonalisationStruct());
        }

        return $contactPersonalisationCollection;
    }
}
