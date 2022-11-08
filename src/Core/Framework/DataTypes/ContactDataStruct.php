<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

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
    public function __construct(string $key, $value)
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
        return $this->key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
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
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
