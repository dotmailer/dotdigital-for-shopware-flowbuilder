<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use Doctrine\DBAL\Connection;
use Dotdigital\Flow\Service\RecipientResolver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Flow\Dispatching\Aware\ContactFormDataAware;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

class RecipientResolverTest extends TestCase
{
    /**
     * @var StringTemplateRenderer|\PHPUnit\Framework\MockObject\MockObject
     */
    private $rendererMock;

    /**
     * @var BusinessEventEncoder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $businessEventEncoderMock;

    /**
     * @var Connection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $connectionMock;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var FlowEvent|\PHPUnit\Framework\MockObject\MockObject
     */
    private $flowEventMock;

    /**
     * @var MailAware|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mailAwareMock;

    /**
     * @var ContactFormDataAware|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contactFormDataAwareMock;

    /**
     * @var RecipientResolver
     */
    private $recipientResolver;

    protected function setUp(): void
    {
        $this->rendererMock = $this->createMock(StringTemplateRenderer::class);
        $this->businessEventEncoderMock = $this->createMock(BusinessEventEncoder::class);
        $this->connectionMock = $this->createMock(Connection::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->flowEventMock = $this->createMock(FlowEvent::class);
        $this->mailAwareMock = $this->createMock(MailAware::class);
        $this->contactFormDataAwareMock = $this->createMock(ContactFormDataAware::class);

        $this->recipientResolver = new RecipientResolver(
            $this->rendererMock,
            $this->businessEventEncoderMock,
            $this->connectionMock,
            $this->loggerMock
        );
    }

    public function testRecipientsForCustomEmail(): void
    {
        $recipients = [
            'type' => 'custom',
            'data' => ['chaz@emailsim.io', 'test@test.test'],
        ];

        $this->businessEventEncoderMock->expects(static::exactly(2))
            ->method('encode')
            ->willReturn([]);

        $this->rendererMock->expects(static::exactly(2))
            ->method('render')
            ->willReturnOnConsecutiveCalls(
                $recipients['data'][0],
                $recipients['data'][1]
            );

        $this->recipientResolver->getRecipients($recipients, $this->flowEventMock);
    }

    public function testRecipientsForAdmin(): void
    {
        $recipients = [
            'type' => 'admin',
        ];

        $admins = [
            ['email' => 'admin@emailsim.io'],
            ['email' => 'bossman@emailsim.io'],
        ];

        $this->connectionMock->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn($admins);

        $this->recipientResolver->getRecipients($recipients, $this->flowEventMock);
    }

    public function testRecipientsForContactFormMail(): void
    {
        $recipients = [
            'type' => 'contactFormMail',
        ];

        $this->flowEventMock->expects(static::once())
            ->method('getEvent')
            ->willReturn($this->contactFormDataAwareMock);

        $this->contactFormDataAwareMock->expects(static::once())
            ->method('getContactFormData')
            ->willReturn([
                'email' => 'chaz@emailsim.io',
            ]);

        $this->recipientResolver->getRecipients($recipients, $this->flowEventMock);
    }

    public function testRecipientsForDefault(): void
    {
        $recipients = [
            'type' => 'default',
        ];

        $this->flowEventMock->expects(static::once())
            ->method('getEvent')
            ->willReturn($this->mailAwareMock);

        $mailRecipientStructMock = $this->createMock(MailRecipientStruct::class);

        $this->mailAwareMock->expects(static::once())
            ->method('getMailStruct')
            ->willReturn($mailRecipientStructMock);

        $mailRecipientStructMock->expects(static::once())
            ->method('getRecipients')
            ->willReturn(['chaz@emailsim.io' => []]);

        $this->recipientResolver->getRecipients($recipients, $this->flowEventMock);
    }
}
