<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class ContactDataFieldStruct
{
    /**
     * @var mixed
     */
    private string $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var string|null
     */
    private $defaultValue;

    /**
     * @param mixed $key
     */
    public function __construct(string $key, $value, $visibility = 'Private', $defaultValue = null)
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
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * Set DefaultValue
     *
     * @param $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}
