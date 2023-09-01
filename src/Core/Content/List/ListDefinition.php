<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Content\List;

use Dotdigital\Flow\Service\Repository\Contracts\DotdigitalDefinitionInterface;
use Dotdigital\Flow\Service\Repository\Contracts\DotdigitalIterableInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ListDefinition extends EntityDefinition implements DotdigitalDefinitionInterface, DotdigitalIterableInterface
{
    public const ENTITY_NAME = 'dotdigital_list';

    public function getApiCall(): string
    {
        return 'getAddressBooks';
    }

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ListCollection::class;
    }

    public function getEntityClass(): string
    {
        return ListEntity::class;
    }

    public function getSearchKeyword(): string
    {
        return 'name';
    }

    public function getCacheLifetime(): int
    {
        return 3600;
    }

    public function getIteratorLimit(): int
    {
        return 1000;
    }

    public function getIterationUpperLimit(): int
    {
        return 5000;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}
