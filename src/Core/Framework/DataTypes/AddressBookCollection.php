<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

final class AddressBookCollection extends EntityCollection
{

	public function getApiAlias(): string
	{
		return 'dotdigital_list_collection';
	}

    /**
     * Collection expected class
     */
    protected function getExpectedClass(): string
    {
        return AddressBookStruct::class;
    }
}
