<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Service\Repository;

use Dotdigital\Flow\Core\Framework\DataTypes\AbstractStruct;
use Dotdigital\Flow\Service\Repository\Contracts\DotdigitalDefinitionInterface;
use Psr\Cache\InvalidArgumentException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldVisibility;
use Shopware\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class DotdigitalEntityReader implements EntityReaderInterface
{
    public function __construct(
        private DotdigitalEntityResolver $entityResolver
    ) {
    }

    /**
     * Fetch data from Dotdigital API and hydrate it into an EntityCollection
     *
     * @throws InvalidArgumentException
     */
    public function read(EntityDefinition $definition, Criteria $criteria, Context $context): EntityCollection
    {
        /** @var \Dotdigital\Flow\Service\Repository\Contracts\DotdigitalDefinitionInterface $definition */
        $lists = $this->entityResolver->fetch($definition, $criteria, $context);
        $collectionClass = $definition->getCollectionClass();
        /** @var \Shopware\Core\Framework\DataAbstractionLayer\EntityCollection $collection */
        $collection = new $collectionClass();
        foreach ($lists as $list) {
            if (\in_array($list->getId(), $criteria->getIds(), true)) {
                $collection->add($this->buildEntityFromDefinition($definition, $list));
            }
        }

        return $collection;
    }

    /**
     * Build an Entity from a Dotdigital Struct and a Definition
     *
     * @return mixed
     */
    private function buildEntityFromDefinition(DotdigitalDefinitionInterface $definition, AbstractStruct $dotdigitalDataStruct)
    {
        $entityClass = $definition->getEntityClass();
        $entity = new $entityClass();
        $visibilityProperties = [];
        foreach ($dotdigitalDataStruct->toArray() as $key => $value) {
            if (method_exists($entity, 'set' . ucfirst($key))) {
                if ($key === 'id') {
                    /** @phpstan-ignore-next-line-pattern 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, callable given.' */
                    $value = sprintf('%s', $value);
                }
                $entity->{'set' . ucfirst($key)}($value);
                $visibilityProperties[] = $key;
            }
        }

        /** @var \Shopware\Core\Framework\DataAbstractionLayer\Entity $entity */
        $entity->internalSetEntityData(
            $definition->getEntityName(),
            new FieldVisibility($visibilityProperties)
        );

        return $entity;
    }
}
