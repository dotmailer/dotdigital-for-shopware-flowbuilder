<?php

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;

trait InteractWithAddressBooksTrait
{
    use UtilitiesTrait;

    /**
     * @return AddressBookStruct
     * @throws \Exception
     */
    protected function generateAddressBook():AddressBookStruct{
        return AddressBookStruct::createFromResponse([
            'id' => $this->generateInteger(),
            'name' => $this->generateRandomString(),
            'visibility' => 'Public',
            'contacts' => $this->generateInteger()
        ]);
    }

    /**
     * @param int $count
     * @return AddressBookCollection
     * @throws \Exception
     */
    protected function generateAddressBookCollection(int $count = 1):AddressBookCollection{
        $addressBooks = new AddressBookCollection();
        for ($i = 0; $i < $count; $i++) {
            $addressBooks->add($this->generateAddressBook());
        }
        return $addressBooks;
    }

}
