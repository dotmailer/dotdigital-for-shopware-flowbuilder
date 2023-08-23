<?php declare(strict_types=1);

namespace Dotdigital\Flow\Service\Repository;

use Dotdigital\Flow\Core\Framework\DataTypes\AbstractStruct;

use Psr\Cache\InvalidArgumentException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldVisibility;
use Shopware\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;

class DotdigitalEntityReader implements EntityReaderInterface
{

	/**
	 * DotdigitalEntityReader constructor.
	 *
	 * @param ContainerInterface $container
	 * @param DotdigitalClientFactory $clientFactory
	 */
	public function __construct(
		private ContainerInterface $container,
		private DotdigitalEntityResolver $entityResolver
	){}

	/**
	 * Fetch data from Dotdigital API and hydrate it into an EntityCollection
	 *
	 * @param EntityDefinition $definition
	 * @param Criteria $criteria
	 * @param Context $context
	 * @return EntityCollection
	 * @throws InvalidArgumentException
	 *
	 */
	public function read(EntityDefinition $definition, Criteria $criteria, Context $context): EntityCollection
	{
		$lists = $this->entityResolver->fetch($definition, $criteria, $context);
		$collectionClass = $definition->getCollectionClass();
		$collection = new $collectionClass();
		foreach ($lists as $list) {
			if (in_array($list->getId(), $criteria->getIds())) {
				$collection->add($this->buildEntityFromDefinition($definition, $list));
			}
		}
		return $collection;
	}

	/**
	 * Build an Entity from a Dotdigital Struct and a Definition
	 *
	 * @param EntityDefinition $definition
	 * @param AbstractStruct $dotdigitalDataStruct
	 * @return mixed
	 */
	private function buildEntityFromDefinition(EntityDefinition $definition, AbstractStruct $dotdigitalDataStruct)
	{
		$entityClass = $definition->getEntityClass();
		$entity = new $entityClass();
		$visibilityProperties = [];
		foreach ($dotdigitalDataStruct->toArray() as $key => $value) {
			if(method_exists($entity, 'set' . ucfirst($key))) {
				if($key === 'id') {
					$value = sprintf('%s',$value);
				}
				$entity->{'set' . ucfirst($key)}($value);
				$visibilityProperties[] = $key;
			}
		}

		$entity->internalSetEntityData(
			$definition::ENTITY_NAME,
			new FieldVisibility($visibilityProperties)
		);

		return $entity;
	}

}
