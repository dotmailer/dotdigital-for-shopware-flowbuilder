<?php
declare(strict_types=1);

namespace Dotdigital\Tests\Traits;

use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookCollection;
use Dotdigital\Flow\Core\Framework\DataTypes\AddressBookStruct;

trait InteractWithAddressBooksTrait
{
    use UtilitiesTrait;

    /**
     * @throws \Exception
     */
    protected function generateAddressBook(): AddressBookStruct
    {
        return AddressBookStruct::createFromResponse([
            'id' => $this->generateInteger(),
            'name' => $this->generateRandomString(),
            'visibility' => 'Public',
            'contacts' => $this->generateInteger(),
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function generateAddressBookCollection(int $count = 1): AddressBookCollection
    {
        $addressBooks = new AddressBookCollection();
        for ($i = 0; $i < $count; ++$i) {
            $addressBooks->add($this->generateAddressBook());
        }

        return $addressBooks;
    }
}
