<?php
declare(strict_types=1);

namespace Dotdigital\Flow\Core\Framework\DataTypes;

use Dotdigital\Flow\Core\Framework\Traits\InteractsWithResponseTrait;
use Dotdigital\Flow\Setting\Defaults;

class CampaignStruct extends AbstractStruct
{
    use InteractsWithResponseTrait;

    public const STATUSES = [
        'Unsent',
        'Sent',
    ];

    protected int $id;

    protected string $name;

    protected string $subject;

    protected string $fromName;

    protected string $replyAction;

    protected string $replyToAddress;

    protected bool $isSplitTest;

    protected string $status;

    protected ?RecipientStruct $fromAddress = null;

    protected ?string $htmlContent;

    protected ?string $plainTextContent;

    /**
     * @param array<string, mixed>|null $fromAddress
     */
    public function __construct(
        int $id,
        string $name = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $subject = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $fromName = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $replyAction = Defaults::DEFAULT_UNDEFINED_VALUE,
        string $replyToAddress = Defaults::DEFAULT_UNDEFINED_VALUE,
        bool $isSplitTest = false,
        string $status = CampaignStruct::STATUSES[0],
        ?string $htmlContent = null,
        ?string $plainTextContent = null,
        ?array $fromAddress = null
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setSubject($subject);
        $this->setFromName($fromName);
        $this->setReplyAction($replyAction);
        $this->setReplyToAddress($replyToAddress);
        $this->setIsSplitTest($isSplitTest);
        $this->setStatus($status);
        $this->setHtmlContent($htmlContent);
        $this->setPlainTextContent($plainTextContent);
        $this->setFromAddress($fromAddress);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setReplyAction(string $replyAction): void
    {
        $this->replyAction = $replyAction;
    }

    public function getReplyAction(): string
    {
        return $this->replyAction;
    }

    public function setReplyToAddress(string $replyToAddress): void
    {
        $this->replyToAddress = $replyToAddress;
    }

    public function getReplyToAddress(): string
    {
        return $this->replyToAddress;
    }

    public function setIsSplitTest(bool $isSplitTest): void
    {
        $this->isSplitTest = $isSplitTest;
    }

    public function getIsSplitTest(): bool
    {
        return $this->isSplitTest;
    }

    public function setStatus(string $status): void
    {
        if (!\in_array($status, static::STATUSES, true)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setHtmlContent(?string $htmlContent): void
    {
        $this->htmlContent = $htmlContent;
    }

    public function getHtmlContent(): ?string
    {
        return $this->htmlContent;
    }

    public function setPlainTextContent(?string $plainTextContent): void
    {
        $this->plainTextContent = $plainTextContent;
    }

    public function getPlainTextContent(): ?string
    {
        return $this->plainTextContent;
    }

    /**
     * @param array<string, mixed>|null $fromAddress
     */
    public function setFromAddress(?array $fromAddress): void
    {
        if (!empty($fromAddress) && \array_key_exists('email', $fromAddress)) {
            $this->fromAddress = new RecipientStruct(
                $fromAddress['email'],
                $fromAddress['id'] ?? null
            );
        }
    }

    public function getFromAddress(): ?RecipientStruct
    {
        return $this->fromAddress;
    }
}
