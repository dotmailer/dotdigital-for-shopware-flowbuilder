<?php declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;

class ContactStruct extends AbstractStruct
{
    use InteractsWithResponseTrait;

    protected ?int $id;

    protected string $email;

    protected string $status;

    protected string $optInType;

    protected string $emailType;

    protected ContactDataCollection $dataFields;

    /**
     * @param ContactDataCollection $dataFields
     */
    public function __construct(
        ?int $id,
        string $email,
        string $status,
        string $optInType,
        string $emailType,
        iterable $dataFields
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

    public function setEmail(string $email = 'Html'): void
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

    public function setOptInType(string $optInType = 'Single'): void
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

    public function getDataFields(): ContactDataCollection
    {
        return $this->dataFields;
    }

    /**
     * @param ContactDataCollection|array<string,mixed> $dataFields
     */
    public function setDataFields(iterable $dataFields): void
    {
        if (!is_array($dataFields) && is_a($dataFields, ContactDataCollection::class)) {
            $this->dataFields = $dataFields;

            return;
        }

        $this->dataFields = new ContactDataCollection();
        foreach ($dataFields as $dataField) {
            $this->dataFields->add(new ContactDataStruct(
                $dataField['key'],
                $dataField['value']
            ));
        }
    }
}
