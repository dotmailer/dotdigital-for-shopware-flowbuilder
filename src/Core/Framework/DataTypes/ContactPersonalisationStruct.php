<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Setting\Defaults;

class ContactPersonalisationStruct extends AbstractStruct
{
    protected string $name;

    /**
     * @var string|int|float|null
     */
    protected $value;

    /**
     * @param string|int|float|null $value
     */
    public function __construct(
        string $name = Defaults::DEFAULT_UNDEFINED_VALUE,
        $value = Defaults::DEFAULT_UNDEFINED_VALUE
    ) {
        $this->setName($name);
        $this->setValue($value);
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

    public function setName(string $name): self
    {
        $this->name = $name;

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
