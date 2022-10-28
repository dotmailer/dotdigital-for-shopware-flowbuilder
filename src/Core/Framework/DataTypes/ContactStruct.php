<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;

class ContactStruct
{
    use InteractsWithResponseTrait;

    private ?int $id;

    private string $email;

    private string $status;

    private string $optInType;

    private string $emailType;

    private ContactDataFieldCollection $dataFields;

    /**
     * @param ContactDataFieldCollection|array<string,mixed> $dataFields
     */
    public function __construct(
        ?int $id,
        string $email,
        string $status,
        string $optInType = 'Single',
        string $emailType = 'Html',
        iterable $dataFields = []
    ) {
        $this->setId($id);
        $this->setEmail($email);
        $this->setStatus($status);
        $this->setOptInType($optInType);
        $this->setEmailType($emailType);
        $this->setDataFields($dataFields);
    }

    /**
     * Get contact email if class is called as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getOptInType(): string
    {
        return $this->optInType;
    }

    public function setOptInType(string $optInType): void
    {
        $this->optInType = $optInType;
    }

    public function getEmailType(): string
    {
        return $this->emailType;
    }

    public function setEmailType(string $emailType): void
    {
        $this->emailType = $emailType;
    }

    public function getDataFields(): ContactDataFieldCollection
    {
        return $this->dataFields;
    }

    /**
     * @param ContactDataFieldCollection|array<string,mixed> $dataFields
     */
    public function setDataFields(iterable $dataFields): void
    {
        if (\is_object($dataFields) && is_a($dataFields, ContactDataFieldCollection::class)) {
            $this->dataFields = $dataFields;

            return;
        }

        $this->dataFields = new ContactDataFieldCollection();
        foreach ($dataFields as $dataField) {
            $this->dataFields->add(new ContactDataFieldStruct(
                $dataField['key'],
                $dataField['value']
            ));
        }
    }
}
