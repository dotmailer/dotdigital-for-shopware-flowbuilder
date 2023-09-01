<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Setting\Defaults;

class ContactStruct extends AbstractStruct
{
    use InteractsWithResponseTrait;

    protected int $id;

    protected string $email;

    protected string $status;

    protected string $optInType;

    protected string $emailType;

    protected ContactDataCollection $dataFields;

    protected ?ContactPersonalisationCollection $personalisation;

    /**
     * @param iterable<int, array<string, mixed>> $dataFields
     */
    public function __construct(
        int $id = Defaults::DEFAULT_NUMERIC_VALUE,
        string $email = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $status = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $optInType = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $emailType = Defaults::DEFAULT_UNDEFINED_VALUE,
        iterable $dataFields = [],
        ?ContactPersonalisationCollection $personalisation = null
    ) {
        $this->setId($id);
        $this->setEmail($email);
        $this->setStatus($status);
        $this->setOptInType($optInType);
        $this->setEmailType($emailType);
        $this->setDataFields($dataFields);
        $this->setPersonalisation($personalisation);
    }

    public function __toString()
    {
        return $this->email;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOptInType(): string
    {
        return $this->optInType;
    }

    public function setOptInType(string $optInType): self
    {
        $this->optInType = $optInType;

        return $this;
    }

    public function getEmailType(): string
    {
        return $this->emailType;
    }

    public function setEmailType(string $emailType): self
    {
        $this->emailType = $emailType;

        return $this;
    }

    public function getDataFields(): ContactDataCollection
    {
        return $this->dataFields;
    }

    /**
     * @param  iterable<int, mixed> $dataFields
     */
    public function setDataFields(iterable $dataFields): self
    {
        $this->dataFields = new ContactDataCollection();
        foreach ($dataFields as $dataField) {
            if (is_a($dataField, ContactDataStruct::class)) {
                $this->dataFields->add($dataField);

                continue;
            }
            $this->dataFields->add(new ContactDataStruct(
                (string) $dataField['key'],
                $dataField['value']
            ));
        }

        return $this;
    }

    public function getPersonalisation(): ContactPersonalisationCollection
    {
        return $this->personalisation ?? new ContactPersonalisationCollection();
    }

    public function setPersonalisation(?ContactPersonalisationCollection $personalisation): ContactStruct
    {
        $this->personalisation = $personalisation;

        return $this;
    }
}
