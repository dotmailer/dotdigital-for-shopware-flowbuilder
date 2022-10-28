<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;

class AddressBookStruct
{
    use InteractsWithResponseTrait;

    private int $id;

    private string $name;

    private string $visibility;

    private int $contacts;

    public function __construct(int $id, string $name = '', string $visibility = 'public', int $contacts = 0)
    {
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
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getContacts(): int
    {
        return $this->contacts;
    }

    public function setContacts(int $contacts): void
    {
        $this->contacts = $contacts;
    }
}
