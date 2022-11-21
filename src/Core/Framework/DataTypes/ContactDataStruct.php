<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Setting\Defaults;

class ContactDataStruct extends AbstractStruct
{
    protected string $key;

    /**
     * @var string|int|float|null
     */
    protected $value;

    /**
     * @param string|int|float|null $value
     */
    public function __construct(
        string $key = Defaults::DEFAULT_UNDEFINED_VALUE,
        $value = Defaults::DEFAULT_UNDEFINED_VALUE
    ) {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * Get name of address book if class is called as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string|int|float|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|int|float|null $value
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
