<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\List;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(ListEntity $entity)
 * @method void            set(string $key, ListEntity $entity)
 * @method ListEntity[]    getIterator()
 * @method ListEntity[]    getElements()
 * @method ListEntity|null get(string $key)
 * @method ListEntity|null first()
 * @method ListEntity|null last()
 */
class ListCollection extends EntityCollection
{
	public function getApiAlias(): string
	{
		return 'dotdigital_list_collection';
	}

	protected function getExpectedClass(): string
	{
		return ListEntity::class;
	}
}
