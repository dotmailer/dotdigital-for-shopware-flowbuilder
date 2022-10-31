<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class ApiDataFieldStruct extends AbstractStruct
{
    protected string $name;

    protected string $type;

    protected string $visibility;

    protected ?string $defaultValue;

    public function __construct(string $name, string $type, string $visibility = 'Private', ?string $defaultValue = null)
    {
        $this->setName($name);
        $this->setType($type);
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
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
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
