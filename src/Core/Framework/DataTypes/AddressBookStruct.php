<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class AddressBookStruct
{
    /**
     * @var string
     */
    private int $id;

    private string $name;

    private string $visibility;

    private int $contacts;

    /**
     * @param string $email
     */
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

    /**
     * Get Id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set Id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get Name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set Name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * Get Contacts
     */
    public function getContacts(): int
    {
        return $this->contacts;
    }

    /**
     * Set Contacts
     */
    public function setContacts(int $contacts): void
    {
        $this->contacts = $contacts;
    }
}
