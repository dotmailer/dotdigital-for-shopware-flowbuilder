<?php

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Shopware\Core\Framework\Struct\Collection;

final class RecipientCollection extends Collection
{
    /**
     * Collection expected class
     *
     * @return string|null
     */
    protected function getExpectedClass(): ?string
    {
        return RecipientStruct::class;
    }
}
