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

    /**
     * Get Key
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set Key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * Get Value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set Value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Get Visibility
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * Set Visibility
     */
    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * Get DefaultValue
     */
    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    /**
     * Set DefaultValue
     */
    public function setDefaultValue(?string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}
