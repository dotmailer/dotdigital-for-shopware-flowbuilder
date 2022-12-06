<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\HasErrorMessageTrait;
use Dotdigital\Flow\Setting\Defaults;
use Shopware\Core\Framework\Struct\Struct;

class AbstractStruct extends Struct
{
    use HasErrorMessageTrait;

    /**
     * Convert struct to array.
     *
     * @return iterable<callable>
     */
    public function toArray(): iterable
    {
        return $this->jsonSerialize();
    }

    /**
     * Check if attribute has API or user assigned value
     */
    public function attributeIsDefault(string $attribute): bool
    {
        if (property_exists($this, $attribute)) {
            $value = $this->{$attribute};
            if (
                $value === Defaults::DEFAULT_UNDEFINED_VALUE
                || $value === Defaults::DEFAULT_DATETIME_VALUE
                || $value === Defaults::DEFAULT_NUMERIC_VALUE
            ) {
                return true;
            }
        }

        return false;
    }
}
