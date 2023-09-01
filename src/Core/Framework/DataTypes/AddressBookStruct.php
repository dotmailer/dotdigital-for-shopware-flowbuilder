<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Setting\Defaults;

class AddressBookStruct extends AbstractStruct
{
    use InteractsWithResponseTrait;

    protected int $id;

    protected string $name;

    protected string $visibility;

    protected int $contacts;

    public function __construct(
        int $id = Defaults::DEFAULT_NUMERIC_VALUE,
        string $name = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $visibility = Defaults::DEFAULT_UNDEFINED_VALUE,
        int $contacts = Defaults::DEFAULT_NUMERIC_VALUE
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setVisibility($visibility);
        $this->setContacts($contacts);
    }

    /**
     * Get name of address book if class is called as a string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    public function getUniqueIdentifier(): string
    {
        return (string) $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getContacts(): int
    {
        return $this->contacts;
    }

    public function setContacts(int $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function isApiReady(): bool
    {
        return !empty($this->getId()) && $this->id !== Defaults::DEFAULT_NUMERIC_VALUE;
    }
}
