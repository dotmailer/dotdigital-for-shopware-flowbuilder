<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class RecipientStruct extends AbstractStruct
{
    protected string $email;

    public function __construct(string $email)
    {
        $this->setEmail($email);
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address: {$email}");
        }
        $this->email = $email;
    }
}
