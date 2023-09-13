<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Setting\Defaults;
use Dotdigital\Flow\Setting\Settings;

class ProgramStruct extends AbstractStruct
{
    use InteractsWithResponseTrait;

    protected int $id;

    protected string $name;

    protected string $status;

    protected \DateTimeImmutable $dateCreated;

    public function __construct(
        int $id,
        string $name = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $status = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $dateCreated = Defaults::DEFAULT_DATETIME_VALUE
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setStatus($status);
        $this->setDateCreated($dateCreated);
    }

    /**
     * Get name of program if class is called as a string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateCreated(): string
    {
        return $this->dateCreated->format(Settings::DATE_TIME_FORMAT);
    }

    public function setDateCreated(string $dateTime): self
    {
        $this->dateCreated = new \DateTimeImmutable($dateTime);

        return $this;
    }
}
