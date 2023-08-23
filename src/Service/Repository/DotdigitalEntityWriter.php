<?php

namespace Dotdigital\Flow\Service\Repository;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\VersionManager;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteResult;

class DotdigitalEntityWriter extends VersionManager implements EntityWriterInterface
{

	public function sync(array $operations, WriteContext $context): WriteResult
	{
		throw new \Exception('Not allowed');
	}

	public function upsert(EntityDefinition $definition, array $rawData, WriteContext $writeContext): array
	{
		throw new \Exception('Not allowed');
	}

	public function insert(EntityDefinition $definition, array $rawData, WriteContext $writeContext): array
	{
		throw new \Exception('Not allowed');
	}

	public function update(EntityDefinition $definition, array $rawData, WriteContext $writeContext): array
	{
		throw new \Exception('Not allowed');
	}

	public function delete(EntityDefinition $definition, array $ids, WriteContext $writeContext): WriteResult
	{
		throw new \Exception('Not allowed');
	}
}
