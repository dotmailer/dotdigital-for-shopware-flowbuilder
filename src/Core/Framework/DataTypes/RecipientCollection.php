<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Shopware\Core\Framework\Struct\Collection;

final class RecipientCollection extends Collection
{
    /**
     * Collection expected class
     */
    protected function getExpectedClass(): ?string
    {
        return RecipientStruct::class;
    }
}
