<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Setting\Defaults;

class ApiDataFieldStruct extends AbstractStruct
{
    protected string $name;

    protected string $type;

    protected string $visibility;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @param string|int|float|null $defaultValue
     */
    public function __construct(
        string $name = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $type = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $visibility = Defaults::DEFAULT_UNDEFINED_VALUE,
        $defaultValue = Defaults::DEFAULT_UNDEFINED_VALUE
    ) {
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return string|int|float|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param string|int|float|null $defaultValue
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }
}
