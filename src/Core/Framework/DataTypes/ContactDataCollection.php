<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\HasErrorMessageTrait;
use Shopware\Core\Framework\Struct\Collection;

class ContactDataCollection extends Collection
{
    use HasErrorMessageTrait;

    /**
     * Collection expected class
     */
    protected function getExpectedClass(): ?string
    {
        return ContactDataStruct::class;
    }
}
