<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Setting\Defaults;
use Dotdigital\Flow\Setting\Settings;

class ProgramEnrolmentStruct extends AbstractStruct
{
    use InteractsWithResponseTrait;

    protected string $id;

    protected int $programId;

    protected string $status;

    protected \DateTimeImmutable $dateCreated;

    protected ContactCollection $contacts;

    /**
     * @param iterable<int> $enrolledContacts
     */
    public function __construct(
        string $id,
        int $programId = Defaults::DEFAULT_NUMERIC_VALUE,
        string $status = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $dateCreated = Defaults::DEFAULT_DATETIME_VALUE,
        iterable $enrolledContacts = []
    ) {
        $this->setId($id);
        $this->setProgramId($programId);
        $this->setStatus($status);
        $this->setDateCreated($dateCreated);
        $this->setContacts($enrolledContacts);
    }

    /**
     * Get name of program if class is called as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getProgramId(): int
    {
        return $this->programId;
    }

    public function setProgramId(int $programId): self
    {
        $this->programId = $programId;

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

    /**
     * @param iterable<int> $contacts
     */
    public function setContacts(iterable $contacts): self
    {
        $this->contacts = new ContactCollection();
        foreach ($contacts as $contact) {
            $this->contacts->add(
                new ContactStruct($contact)
            );
        }

        return $this;
    }

    public function getContact(): ContactCollection
    {
        return $this->contacts;
    }
}
