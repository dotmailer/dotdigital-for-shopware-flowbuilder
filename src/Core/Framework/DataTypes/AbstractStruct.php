<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class AbstractStruct
{
    /**
     * Convert struct to array.
     *
     * @return iterable<callable>
     */
    public function toArray(): iterable
    {
        return get_object_vars($this);
    }
}
