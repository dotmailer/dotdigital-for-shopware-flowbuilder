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

    /**
     * Get Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set Id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get Email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set Email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Get Status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set Status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get OptInType
     */
    public function getOptInType(): string
    {
        return $this->optInType;
    }

    /**
     * Set OptInType
     */
    public function setOptInType(string $optInType): void
    {
        $this->optInType = $optInType;
    }

    /**
     * Get EmailType
     */
    public function getEmailType(): string
    {
        return $this->emailType;
    }

    /**
     * Set EmailType
     */
    public function setEmailType(string $emailType): void
    {
        $this->emailType = $emailType;
    }

    /**
     * Get DataFields
     */
    public function getDataFields(): ContactDataFieldCollection
    {
        return $this->dataFields;
    }

    /**
     * Set DataFields
     *
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
