<?php declare(strict_types=1);

namespace Dotdigital\Tests;

use DG\BypassFinals;
use Doctrine\DBAL\Connection;
use Dotdigital\Flow\Core\Content\Flow\Dispatching\Action\DotdigitalEmailSenderAction;
use Dotdigital\Flow\Core\Framework\DataTypes\RecipientCollection;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Flow\Service\RecipientResolver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Webhook\BusinessEventEncoder;

class DotdigitalEmailSenderActionTest extends TestCase
{
    /**
     * @var DotdigitalEmailSenderAction
     */
    private $dotdigitalSenderAction;

    /**
     * @var DotdigitalClientFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dotdigitalClientFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|StringTemplateRenderer
     */
    private $rendererMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|BusinessEventEncoder
     */
    private $businessEventLoaderMock;

    /**
     * @var Connection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $connectionMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private $loggerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MailAware
     */
    private $mailAwareMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MailRecipientStruct
     */
    private $mailRecipientStructMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|FlowEvent
     */
    private $eventMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Context
     */
    private $contextMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ContactFormEvent
     */
    private $contactFormEventMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ContextSource
     */
    private $contextSourceMock;

    protected function setUp(): void
    {
        BypassFinals::enable();
        $this->dotdigitalClientFactoryMock = $this->createMock(DotdigitalClientFactory::class);
        $this->rendererMock = $this->createMock(StringTemplateRenderer::class);
        $this->businessEventLoaderMock = $this->createMock(BusinessEventEncoder::class);
        $this->connectionMock = $this->createMock(Connection::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->mailAwareMock = $this->createMock(MailAware::class);
        $this->mailRecipientStructMock = $this->createMock(MailRecipientStruct::class);
        $this->eventMock = $this->createMock(FlowEvent::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->recipientResolverMock = $this->createMock(RecipientResolver::class);
        /* @phpstan-ignore-next-line */
        $this->contactFormEventMock = $this->createMock(ContactFormEvent::class);
        $this->contextSourceMock = $this->getMockBuilder(ContextSource::class)
            ->addMethods(['getSalesChannelId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipientCollectionMock = $this->getMockBuilder(RecipientCollection::class)
            ->addMethods(['getEmail'])
            ->onlyMethods(['getElements', 'count'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipientResolverMock->expects(static::once())
            ->method('getRecipients')
            ->willReturn($this->recipientCollectionMock);

        $this->recipientCollectionMock->expects(static::once())
            ->method('count')
            ->willReturn(1);

        $this->dotdigitalSenderAction = new DotdigitalEmailSenderAction(
            $this->dotdigitalClientFactoryMock,
            $this->businessEventLoaderMock,
            $this->loggerMock,
            $this->recipientResolverMock
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalEmailSenderCustomerEmail(): void
    {
        $this->eventMock->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->mailAwareMock);

        $this->recipientCollectionMock->expects(static::atLeastOnce())
            ->method('count')
            ->willReturn(1);

        $this->eventMock->expects(static::once())
            ->method('getConfig')
            ->willReturn([
                'recipient' => [
                    'type' => 'default',
                    'data' => [],
                ],
                'campaignId' => 100000,
            ]);

        $this->eventMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalSenderAction->handle($this->eventMock);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalEmailSenderContactFormEmail(): void
    {
        $this->eventMock->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->contactFormEventMock);

        $this->eventMock->expects(static::once())
            ->method('getConfig')
            ->willReturn([
                'recipient' => [
                    'type' => 'contactFormMail',
                    'data' => [],
                ],
                'campaignId' => 100000,
            ]);

        $this->eventMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalSenderAction->handle($this->eventMock);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDotdigitalEmailSenderAdminEmail(): void
    {
        $this->eventMock->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->mailAwareMock);

        $this->eventMock->expects(static::once())
            ->method('getConfig')
            ->willReturn([
                'recipient' => [
                    'type' => 'admin',
                    'data' => [],
                ],
                'campaignId' => 100000,
            ]);

        $this->eventMock->expects(static::once())
            ->method('getContext')
            ->willReturn($this->contextMock);

        $this->contextMock->expects(static::once())
            ->method('getSource')
            ->willReturn($this->contextSourceMock);

        $this->dotdigitalSenderAction->handle($this->eventMock);
    }
}
