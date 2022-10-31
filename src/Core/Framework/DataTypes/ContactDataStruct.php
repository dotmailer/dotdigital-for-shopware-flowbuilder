<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class ContactDataStruct extends AbstractStruct
{
    protected string $key;

    protected string $value;

    public function __construct(string $key, string $value)
    {
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
        return $this->value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
