<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Shopware\Core\Framework\Struct\Collection;

class ContactDataCollection extends Collection
{
    /**
     * Collection expected class
     */
    protected function getExpectedClass(): ?string
    {
        return ContactDataStruct::class;
    }
}
