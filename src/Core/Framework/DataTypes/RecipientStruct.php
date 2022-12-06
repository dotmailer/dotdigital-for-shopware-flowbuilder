<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class RecipientStruct extends AbstractStruct
{
    protected string $email;

    protected ?int $id;

    public function __construct(
        string $email,
        ?int $id = null
    ) {
        $this->setEmail($email);
        $this->setId($id);
    }

    /**
     * Get email if class is called as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        if (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address: {$email}");
        }
        $this->email = $email;

        return $this;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
