<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class ContactDataFieldStruct
{
    private string $key;

    private string $value;

    private string $visibility;

    private ?string $defaultValue;

    public function __construct(string $key, ?string $value, string $visibility = 'Private', ?string $defaultValue = null)
    {
        $this->setKey($key);
        $this->setValue($value);
        $this->setVisibility($visibility);
        $this->setDefaultValue($defaultValue);
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

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}
