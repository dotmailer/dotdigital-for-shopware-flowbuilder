<?php

namespace Dotdigital\Flow\Core\Framework\DataTypes;

class RecipientStruct
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->setEmail($email);
    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set Email
     *
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new \InvalidArgumentException("Invalid email address: {$email}");
        }
        $this->email = $email;
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
}
